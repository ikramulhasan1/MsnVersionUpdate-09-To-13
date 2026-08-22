<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ImageProcessing\DuplicateAltDetector;
use App\ImageProcessing\Exceptions\InvalidImageException;
use App\ImageProcessing\ImageJobService;
use App\ImageProcessing\ImageSeoScorer;
use App\ImageProcessing\SmartMetadataGenerator;
use App\Models\ImageProcessingItem;
use App\Models\ImageProcessingJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Image Everything (Phase S3 — Image SEO / Smart Metadata Generator).
 * PRODUCTION-CRITICAL OWNERSHIP PATTERN, same as
 * App\Http\Controllers\TechnicalSeoController's own docblock: every
 * action receiving an ImageProcessingJob/ImageProcessingItem verifies
 * ownership via authorizeOwner()/authorizeItem() and aborts 403/404
 * otherwise — no route-level scope, ownership is enforced here.
 *
 * UPLOAD + GENERATION HAPPENS SYNCHRONOUSLY, deliberately NOT queued:
 * unlike Phase S2's own App\ImageProcessing\Jobs\AnalyzeImageMetadataJob
 * (real Imagick pixel analysis, genuinely worth moving off the request
 * thread), everything App\ImageProcessing\SmartMetadataGenerator does
 * is cheap string templating against data App\ImageProcessing\ImageJobService::uploadImage()
 * already puts on the item synchronously (original filename, width,
 * height, format, file size) — there is nothing worth a queue round-
 * trip here, and doing it inline means a person sees their generated
 * metadata the moment their upload finishes, no polling page needed.
 */
final class ImageSeoController extends Controller
{
    public function __construct(
        private readonly ImageJobService $imageJobService,
        private readonly SmartMetadataGenerator $smartMetadataGenerator,
        private readonly DuplicateAltDetector $duplicateAltDetector,
        private readonly ImageSeoScorer $imageSeoScorer,
    ) {}

    public function index(Request $request): View
    {
        return view('image-seo.index', [
            'jobs' => $request->user()->imageProcessingJobs()->latest()->limit(10)->get(),
            'purposes' => config('image-processing.image_seo.purposes', []),
            'maxImages' => config('image-processing.image_seo.max_images_per_batch', 20),
        ]);
    }

    /**
     * One submission: the "Image Context" form fields plus one or more
     * files, all together — see this class's own docblock for why
     * generation happens right here rather than behind a progress
     * page.
     */
    public function store(Request $request): RedirectResponse
    {
        $maxImages = (int) config('image-processing.image_seo.max_images_per_batch', 20);
        $purposeKeys = array_keys(config('image-processing.image_seo.purposes', []));

        $validated = $request->validate([
            'primary_keyword' => ['nullable', 'string', 'max:255'],
            'secondary_keywords' => ['nullable', 'string', 'max:500'],
            'page_topic' => ['nullable', 'string', 'max:255'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'target_audience' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', Rule::in($purposeKeys)],
            'images' => ['required', 'array', 'min:1', "max:{$maxImages}"],
            'images.*' => ['file'],
        ]);

        $context = $this->buildContext($validated);

        $job = $this->imageJobService->createJob($request->user());
        $job->forceFill(['context' => $context])->save();

        $failures = [];

        foreach ($request->file('images', []) as $file) {
            try {
                $item = $this->imageJobService->uploadImage($job, $file);
                $result = $this->smartMetadataGenerator->generate($item, $context);
                $item->forceFill(['result' => $result])->save();
            } catch (InvalidImageException $e) {
                $failures[] = $e->getMessage();
            }
        }

        if ($job->items()->count() === 0) {
            return back()->withErrors(['images' => 'None of the uploaded files could be processed. '.implode(' ', $failures)])->withInput();
        }

        $this->refreshDuplicatesAndScores($job);

        $redirect = redirect()->route('image-seo.show', $job);

        return $failures === [] ? $redirect : $redirect->with('upload_warnings', $failures);
    }

    public function show(Request $request, ImageProcessingJob $job): View
    {
        $this->authorizeOwner($request, $job);

        return view('image-seo.show', [
            'job' => $job,
            'items' => $job->items()->orderBy('id')->get(),
            'altStyles' => config('image-processing.image_seo.alt_styles', []),
            'purposes' => config('image-processing.image_seo.purposes', []),
        ]);
    }

    /**
     * Streams the ORIGINAL upload's own bytes back inline, straight
     * off the 'private-images' disk — the only way this page can show
     * a thumbnail at all, since (see App\Models\ImageProcessingJob's
     * own migration docblock) nothing this feature stores is ever
     * reachable by a plain public URL. Ownership-checked like every
     * other action here, never a raw unauthenticated file route.
     */
    public function preview(Request $request, ImageProcessingJob $job, ImageProcessingItem $item): StreamedResponse
    {
        $this->authorizeItem($request, $job, $item);

        abort_unless(Storage::disk('private-images')->exists($item->temp_path), 404);

        return Storage::disk('private-images')->response($item->temp_path, null, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Re-runs App\ImageProcessing\SmartMetadataGenerator for ONE field
     * on ONE item, cycling the template variant each call (via the
     * item's own persisted generation_count) so repeated clicks give
     * genuinely different wording rather than the same suggestion back
     * — never touches any OTHER field on this item.
     */
    public function regenerate(Request $request, ImageProcessingJob $job, ImageProcessingItem $item): JsonResponse
    {
        $this->authorizeItem($request, $job, $item);

        $validated = $request->validate([
            'field' => ['required', 'string', Rule::in(['filename', 'title', 'caption', 'description', 'alt'])],
        ]);

        $result = $item->result ?? [];
        $variant = ((int) data_get($result, 'generation_count', 0)) + 1;
        $result['generation_count'] = $variant;

        $fresh = $this->smartMetadataGenerator->generate($item, $job->context ?? [], $variant);
        $field = $validated['field'];

        if ($field === 'alt') {
            $keepStyle = (string) data_get($result, 'alt.active_style', 'seo');
            $fresh['alt']['active_style'] = $keepStyle;
            $fresh['alt']['active_index'] = 0;
            $result['alt'] = $fresh['alt'];
        } else {
            $result[$field] = $fresh[$field];
        }

        $item->forceFill(['result' => $result])->save();
        $this->refreshDuplicatesAndScores($job);
        $item->refresh();

        return response()->json(['field' => $field, 'result' => $item->result]);
    }

    /**
     * Persists the person's own choice for a field — either picking
     * one of the generated candidates (alt: {style, index}) or a fully
     * free-typed replacement (any field: {value}) — see this app's own
     * "generated text is never forced on anyone" requirement in this
     * class's own docblock.
     */
    public function select(Request $request, ImageProcessingJob $job, ImageProcessingItem $item): JsonResponse
    {
        $this->authorizeItem($request, $job, $item);

        $validated = $request->validate([
            'field' => ['required', 'string', Rule::in(['filename', 'title', 'caption', 'description', 'alt'])],
            'value' => ['nullable', 'string', 'max:1000'],
            'style' => ['nullable', 'string', Rule::in(array_keys(config('image-processing.image_seo.alt_styles', [])))],
            'index' => ['nullable', 'integer', 'min:0', 'max:2'],
        ]);

        $result = $item->result ?? [];
        $field = $validated['field'];

        if ($field === 'alt') {
            $style = $validated['style'] ?? (string) data_get($result, 'alt.active_style', 'seo');

            if (array_key_exists('index', $validated) && $validated['index'] !== null && ! isset($validated['value'])) {
                $result['alt']['active_style'] = $style;
                $result['alt']['active_index'] = (int) $validated['index'];
            } elseif (isset($validated['value']) && trim($validated['value']) !== '') {
                $result['alt']['styles'][$style]['candidates'][0] = [
                    'text' => trim($validated['value']),
                    'auto_adjusted' => false,
                    'source' => 'manual',
                    'note' => null,
                ];
                $result['alt']['active_style'] = $style;
                $result['alt']['active_index'] = 0;
            }
        } elseif (isset($validated['value']) && trim($validated['value']) !== '') {
            $value = trim($validated['value']);
            $result[$field]['selected'] = $value;
            $result[$field]['candidates'][0] = ['text' => $value, 'auto_adjusted' => false, 'source' => 'manual', 'note' => null];
        }

        $item->forceFill(['result' => $result])->save();
        $this->refreshDuplicatesAndScores($job);
        $item->refresh();

        return response()->json(['field' => $field, 'result' => $item->result]);
    }

    public function export(Request $request, ImageProcessingJob $job, string $format): StreamedResponse
    {
        $this->authorizeOwner($request, $job);
        abort_unless(in_array($format, ['json', 'csv'], true), 404);

        $rows = $job->items()->orderBy('id')->get()->map(fn (ImageProcessingItem $item): array => $this->exportRow($item));

        if ($format === 'json') {
            return Response::streamDownload(function () use ($rows): void {
                echo $rows->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }, "image-seo-{$job->uuid}.json", ['Content-Type' => 'application/json']);
        }

        return Response::streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Original Filename', 'Suggested Filename', 'Title', 'Alt Text', 'Caption', 'Description', 'Width', 'Height', 'Format', 'SEO Score', 'Img HTML']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['original_filename'], $row['filename'], $row['title'], $row['alt_text'],
                    $row['caption'], $row['description'], $row['width'], $row['height'],
                    $row['format'], $row['seo_score'], $row['html'],
                ]);
            }

            fclose($handle);
        }, "image-seo-{$job->uuid}.csv");
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function buildContext(array $validated): array
    {
        $secondaryKeywords = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($validated['secondary_keywords'] ?? '')),
        ), static fn (string $k): bool => $k !== ''));

        return [
            'primary_keyword' => trim((string) ($validated['primary_keyword'] ?? '')),
            'secondary_keywords' => $secondaryKeywords,
            'page_topic' => trim((string) ($validated['page_topic'] ?? '')),
            'product_name' => trim((string) ($validated['product_name'] ?? '')),
            'brand' => trim((string) ($validated['brand'] ?? '')),
            'category' => trim((string) ($validated['category'] ?? '')),
            'target_audience' => trim((string) ($validated['target_audience'] ?? '')),
            'purpose' => (string) ($validated['purpose'] ?? ''),
        ];
    }

    /**
     * The ONE place App\ImageProcessing\DuplicateAltDetector and
     * App\ImageProcessing\ImageSeoScorer actually run — called after
     * every action that could change an item's own selected text
     * (store/regenerate/select) so $result['duplicate_alt'] and
     * $result['checklist'] are always in sync with what's currently
     * SELECTED, never stale from a previous edit.
     */
    private function refreshDuplicatesAndScores(ImageProcessingJob $job): void
    {
        $items = $job->items()->get();
        $context = $job->context ?? [];

        $entries = $items->map(function (ImageProcessingItem $item): array {
            $result = $item->result ?? [];
            $style = (string) data_get($result, 'alt.active_style', 'seo');
            $index = (int) data_get($result, 'alt.active_index', 0);

            return [
                'id' => $item->id,
                'alt' => (string) data_get($result, "alt.styles.{$style}.candidates.{$index}.text", ''),
                'label' => $item->original_filename,
            ];
        })->all();

        $duplicates = $this->duplicateAltDetector->detect($entries);

        foreach ($items as $item) {
            $result = $item->result ?? [];
            $result['duplicate_alt'] = $duplicates[$item->id] ?? ['is_duplicate' => false, 'similar_to' => [], 'suggestion' => null];
            $result['checklist'] = $this->imageSeoScorer->score($item, $context, $result);
            $item->forceFill(['result' => $result])->save();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function exportRow(ImageProcessingItem $item): array
    {
        $result = $item->result ?? [];
        $style = (string) data_get($result, 'alt.active_style', 'seo');
        $index = (int) data_get($result, 'alt.active_index', 0);
        $alt = (string) data_get($result, "alt.styles.{$style}.candidates.{$index}.text", '');
        $filename = (string) data_get($result, 'filename.selected', $item->original_filename);
        $title = (string) data_get($result, 'title.selected', '');

        return [
            'original_filename' => $item->original_filename,
            'filename' => $filename,
            'title' => $title,
            'alt_text' => $alt,
            'caption' => (string) data_get($result, 'caption.selected', ''),
            'description' => (string) data_get($result, 'description.selected', ''),
            'width' => $item->width,
            'height' => $item->height,
            'format' => $item->format,
            'seo_score' => data_get($result, 'checklist.score'),
            'html' => self::imgHtml($filename, $alt, $title, $item->width, $item->height),
        ];
    }

    /**
     * Public + static so resources/views/image-seo/show.blade.php can
     * render the exact same markup a person sees in the "copy this
     * code" box that export() streams out — one implementation, never
     * two copies of this string that could drift apart.
     */
    public static function imgHtml(string $filename, string $alt, string $title, ?int $width, ?int $height): string
    {
        return sprintf(
            '<img src="/images/%s" alt="%s" title="%s" width="%d" height="%d" loading="lazy">',
            $filename,
            htmlspecialchars($alt, ENT_QUOTES),
            htmlspecialchars($title, ENT_QUOTES),
            (int) $width,
            (int) $height,
        );
    }

    private function authorizeOwner(Request $request, ImageProcessingJob $job): void
    {
        abort_unless($job->user_id === $request->user()->id, 403);
    }

    private function authorizeItem(Request $request, ImageProcessingJob $job, ImageProcessingItem $item): void
    {
        $this->authorizeOwner($request, $job);
        abort_unless($item->job_id === $job->id, 404);
    }
}