<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ImageItemStatus;
use App\ImageProcessing\Exceptions\InvalidImageException;
use App\ImageProcessing\ImageJobService;
use App\ImageProcessing\ImageStudioRecommender;
use App\ImageProcessing\Jobs\ProcessImageStudioOperationJob;
use App\Models\ImageProcessingItem;
use App\Models\ImageProcessingJob;
use App\Models\ImageProcessingOperation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

/**
 * Image Everything (Phase S4 — Image Studio: Resize/Compress/Convert).
 * PRODUCTION-CRITICAL OWNERSHIP PATTERN, identical to
 * App\Http\Controllers\ImageSeoController's own docblock: every action
 * receiving a job/item/operation verifies ownership through the chain
 * (job -> item -> operation) and aborts 403/404 otherwise.
 *
 * EVERY ACTUAL PROCESSING RUN IS QUEUED — storeOperation() only ever
 * creates an ImageProcessingOperation row and dispatches
 * App\ImageProcessing\Jobs\ProcessImageStudioOperationJob, it never
 * runs App\ImageProcessing\ImageStudioProcessor itself. This is the
 * OPPOSITE of Phase S3's own ImageSeoController (synchronous string
 * templating there) precisely because real Imagick encode/decode work
 * is genuinely expensive — see ProcessImageStudioOperationJob's own
 * docblock.
 */
final class ImageStudioController extends Controller
{
    public function __construct(
        private readonly ImageJobService $imageJobService,
        private readonly ImageStudioRecommender $recommender,
    ) {}

    public function index(Request $request): View
    {
        return view('image-studio.index', [
            'jobs' => $request->user()->imageProcessingJobs()->latest()->limit(10)->get(),
            'maxImages' => config('image-processing.image_studio.max_images_per_batch', 20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $maxImages = (int) config('image-processing.image_studio.max_images_per_batch', 20);

        $request->validate([
            'images' => ['required', 'array', 'min:1', "max:{$maxImages}"],
            'images.*' => ['file'],
        ]);

        $job = $this->imageJobService->createJob($request->user());
        $failures = [];

        foreach ($request->file('images', []) as $file) {
            try {
                $this->imageJobService->uploadImage($job, $file);
            } catch (InvalidImageException $e) {
                $failures[] = $e->getMessage();
            }
        }

        if ($job->items()->count() === 0) {
            return back()->withErrors(['images' => 'None of the uploaded files could be processed. '.implode(' ', $failures)])->withInput();
        }

        $redirect = redirect()->route('image-studio.show', $job);

        return $failures === [] ? $redirect : $redirect->with('upload_warnings', $failures);
    }

    public function show(Request $request, ImageProcessingJob $job): View
    {
        $this->authorizeOwner($request, $job);

        $items = $job->items()->with('operations')->orderBy('id')->get();

        $recommendations = [];

        foreach ($items as $item) {
            $recommendations[$item->id] = $this->recommender->recommendFormat((string) $item->format);
        }

        return view('image-studio.show', [
            'job' => $job,
            'items' => $items,
            'formatRecommendations' => $recommendations,
            'resizePresets' => config('image-processing.image_studio.resize_presets', []),
            'thumbnailWidth' => config('image-processing.image_studio.thumbnail_width', 200),
            'smartResizePresets' => config('image-processing.image_studio.smart_resize_presets', []),
            'cropRatios' => config('image-processing.image_studio.crop_ratios', []),
            'compressionModes' => config('image-processing.image_studio.compression_modes', []),
            'formats' => config('image-processing.image_studio.formats', []),
            'responsiveWidths' => config('image-processing.image_studio.responsive_widths', []),
        ]);
    }

    public function previewOriginal(Request $request, ImageProcessingJob $job, ImageProcessingItem $item): StreamedResponse
    {
        $this->authorizeItem($request, $job, $item);

        abort_unless(Storage::disk('private-images')->exists($item->temp_path), 404);

        return Storage::disk('private-images')->response($item->temp_path, null, ['Cache-Control' => 'private, max-age=3600']);
    }

    /**
     * Inline preview of a completed operation's OWN output — the
     * "after" side of the before/after comparison slider. For a
     * 'responsive' operation, ?width= picks which variant (defaults to
     * the largest).
     */
    public function previewOperation(Request $request, ImageProcessingJob $job, ImageProcessingItem $item, ImageProcessingOperation $operation): StreamedResponse
    {
        $this->authorizeOperation($request, $job, $item, $operation);

        $path = $this->resolveOperationFilePath($operation, $request->query('width'));
        abort_unless($path !== null && Storage::disk('private-images')->exists($path), 404);

        return Storage::disk('private-images')->response($path, null, ['Cache-Control' => 'private, max-age=3600']);
    }

    /**
     * Single-file download (attachment) of a completed operation's own
     * output. Same variant-selection rule as previewOperation().
     */
    public function downloadOperation(Request $request, ImageProcessingJob $job, ImageProcessingItem $item, ImageProcessingOperation $operation): BinaryFileResponse
    {
        $this->authorizeOperation($request, $job, $item, $operation);

        $path = $this->resolveOperationFilePath($operation, $request->query('width'));
        abort_unless($path !== null, 404);

        $absolute = Storage::disk('private-images')->path($path);
        abort_unless(is_file($absolute), 404);

        return response()->download($absolute, basename($path));
    }

    /**
     * Bulk download — every COMPLETED operation's own output, across
     * every item in this job, zipped on the fly straight from Phase
     * S1's own temporary 'private-images' disk storage (nothing here
     * is copied anywhere more permanent, and the zip itself is a throwaway
     * temp file deleted the moment it finishes streaming). Per-item
     * subfolder, per-operation-type subfolder within that, so a batch
     * with several operations per image never collides on filenames.
     */
    public function downloadZip(Request $request, ImageProcessingJob $job): BinaryFileResponse
    {
        $this->authorizeOwner($request, $job);

        $items = $job->items()->with('operations')->orderBy('id')->get();

        $tmpDir = storage_path('app/private/image-processing-tmp');

        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $zipPath = $tmpDir.'/'.$job->uuid.'-'.Str::random(8).'.zip';
        $zip = new ZipArchive();

        abort_unless($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'Could not create ZIP archive.');

        $fileCount = 0;

        foreach ($items as $item) {
            $baseFolder = Str::slug(pathinfo($item->original_filename, PATHINFO_FILENAME)) ?: "image-{$item->id}";

            foreach ($item->operations as $operation) {
                if ($operation->status !== ImageItemStatus::COMPLETED) {
                    continue;
                }

                $result = $operation->result ?? [];

                if ($operation->type === 'responsive') {
                    foreach (($result['variants'] ?? []) as $variant) {
                        if ($this->addFileToZip($zip, $variant['path'], "{$baseFolder}/responsive/".basename($variant['path']))) {
                            $fileCount++;
                        }
                    }
                } elseif (! empty($result['path']) && $this->addFileToZip($zip, $result['path'], "{$baseFolder}/{$operation->type}/".basename($result['path']))) {
                    $fileCount++;
                }
            }
        }

        $zip->close();

        if ($fileCount === 0) {
            @unlink($zipPath);
            abort(404, 'No completed operations to download yet.');
        }

        return response()->download($zipPath, "image-studio-{$job->uuid}.zip")->deleteFileAfterSend(true);
    }

    /**
     * Creates ONE operation and queues it — see this class's own
     * docblock for why this never runs
     * App\ImageProcessing\ImageStudioProcessor synchronously.
     */
    public function storeOperation(Request $request, ImageProcessingJob $job, ImageProcessingItem $item): JsonResponse
    {
        $this->authorizeItem($request, $job, $item);

        $type = $request->validate([
            'type' => ['required', 'string', Rule::in(['resize', 'crop', 'compress', 'convert', 'responsive'])],
        ])['type'];

        $params = match ($type) {
            'resize' => $this->resolveResizeParams($request),
            'crop' => $this->resolveCropParams($request, $item),
            'compress' => $this->resolveCompressParams($request),
            'convert' => $this->resolveConvertParams($request),
            'responsive' => $this->resolveResponsiveParams($request),
        };

        $operation = $item->operations()->create([
            'type' => $type,
            'status' => ImageItemStatus::PENDING,
            'params' => $params,
        ]);

        ProcessImageStudioOperationJob::dispatch($operation);

        return response()->json([
            'operation' => $this->presentOperation($job, $item, $operation),
        ]);
    }

    public function pollOperation(Request $request, ImageProcessingJob $job, ImageProcessingItem $item, ImageProcessingOperation $operation): JsonResponse
    {
        $this->authorizeOperation($request, $job, $item, $operation);

        return response()->json([
            'operation' => $this->presentOperation($job, $item, $operation->fresh()),
        ]);
    }

    /**
     * @return array{width: ?int, height: ?int, maintain_aspect: bool}
     */
    private function resolveResizeParams(Request $request): array
    {
        $validated = $request->validate([
            'smart_preset' => ['nullable', 'string'],
            'width' => ['nullable', 'integer', 'min:1', 'max:8000'],
            'height' => ['nullable', 'integer', 'min:1', 'max:8000'],
            'maintain_aspect' => ['nullable', 'boolean'],
        ]);

        if (! empty($validated['smart_preset'])) {
            $dimensions = $this->recommender->smartResizeDimensions($validated['smart_preset']);
            abort_unless($dimensions !== null, 422, 'Unknown Smart Resize preset.');

            return ['width' => $dimensions['width'], 'height' => $dimensions['height'], 'maintain_aspect' => true];
        }

        abort_if(empty($validated['width']) && empty($validated['height']), 422, 'Provide a width, a height, or a Smart Resize preset.');

        return [
            'width' => isset($validated['width']) ? (int) $validated['width'] : null,
            'height' => isset($validated['height']) ? (int) $validated['height'] : null,
            'maintain_aspect' => (bool) ($validated['maintain_aspect'] ?? true),
        ];
    }

    /**
     * @return array{x: int, y: int, width: int, height: int}
     */
    private function resolveCropParams(Request $request, ImageProcessingItem $item): array
    {
        $validated = $request->validate([
            'ratio' => ['nullable', 'string'],
            'x' => ['nullable', 'integer', 'min:0'],
            'y' => ['nullable', 'integer', 'min:0'],
            'width' => ['nullable', 'integer', 'min:1'],
            'height' => ['nullable', 'integer', 'min:1'],
        ]);

        // Manual/user-specified rectangle takes priority when present
        // (a person actually dragged a selection) — see
        // App\ImageProcessing\ImageStudioRecommender's own docblock:
        // this app never guesses a rectangle when the person already
        // gave one.
        if (isset($validated['x'], $validated['y'], $validated['width'], $validated['height'])) {
            return [
                'x' => (int) $validated['x'],
                'y' => (int) $validated['y'],
                'width' => (int) $validated['width'],
                'height' => (int) $validated['height'],
            ];
        }

        abort_unless(! empty($validated['ratio']), 422, 'Provide either a crop rectangle or a ratio preset.');

        $rect = $this->recommender->cropRectForRatio((int) $item->width, (int) $item->height, $validated['ratio']);
        abort_unless($rect !== null, 422, 'Unknown crop ratio, or "free" requires a manual rectangle.');

        return $rect;
    }

    /**
     * @return array{quality: int}
     */
    private function resolveCompressParams(Request $request): array
    {
        $validated = $request->validate([
            'mode' => ['nullable', 'string'],
            'quality' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        if (isset($validated['quality'])) {
            return ['quality' => (int) $validated['quality']];
        }

        abort_unless(! empty($validated['mode']), 422, 'Provide a quality value or a compression mode.');

        return ['quality' => $this->recommender->qualityForMode($validated['mode'])];
    }

    /**
     * @return array{format: string, quality: ?int}
     */
    private function resolveConvertParams(Request $request): array
    {
        $validated = $request->validate([
            'format' => ['required', 'string', Rule::in(config('image-processing.image_studio.formats', []))],
            'quality' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        return [
            'format' => $validated['format'],
            'quality' => isset($validated['quality']) ? (int) $validated['quality'] : null,
        ];
    }

    /**
     * @return array{widths: list<int>}
     */
    private function resolveResponsiveParams(Request $request): array
    {
        $validated = $request->validate([
            'widths' => ['nullable', 'array'],
            'widths.*' => ['integer', 'min:1', 'max:8000'],
        ]);

        $widths = ! empty($validated['widths'])
            ? array_map('intval', $validated['widths'])
            : config('image-processing.image_studio.responsive_widths', [400, 800, 1200, 1600, 2000]);

        return ['widths' => array_values($widths)];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentOperation(ImageProcessingJob $job, ImageProcessingItem $item, ImageProcessingOperation $operation): array
    {
        return [
            'id' => $operation->id,
            'type' => $operation->type,
            'status' => $operation->status->value,
            'result' => $operation->result,
            'error_message' => $operation->error_message,
            'poll_url' => route('image-studio.items.operations.poll', [$job, $item, $operation]),
            'preview_url' => route('image-studio.items.operations.preview', [$job, $item, $operation]),
            'download_url' => route('image-studio.items.operations.download', [$job, $item, $operation]),
        ];
    }

    private function resolveOperationFilePath(ImageProcessingOperation $operation, mixed $widthQuery): ?string
    {
        if ($operation->status !== ImageItemStatus::COMPLETED) {
            return null;
        }

        $result = $operation->result ?? [];

        if ($operation->type === 'responsive') {
            $variants = $result['variants'] ?? [];

            if ($variants === []) {
                return null;
            }

            if ($widthQuery !== null) {
                foreach ($variants as $variant) {
                    if ((int) $variant['width'] === (int) $widthQuery) {
                        return $variant['path'];
                    }
                }
            }

            $largest = end($variants);

            return $largest['path'] ?? null;
        }

        return $result['path'] ?? null;
    }

    private function addFileToZip(ZipArchive $zip, string $relativeStoragePath, string $nameInZip): bool
    {
        $absolute = Storage::disk('private-images')->path($relativeStoragePath);

        if (! is_file($absolute)) {
            return false;
        }

        return $zip->addFile($absolute, $nameInZip);
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

    private function authorizeOperation(Request $request, ImageProcessingJob $job, ImageProcessingItem $item, ImageProcessingOperation $operation): void
    {
        $this->authorizeItem($request, $job, $item);
        abort_unless($operation->item_id === $item->id, 404);
    }
}