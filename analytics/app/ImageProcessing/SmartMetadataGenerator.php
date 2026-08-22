<?php

declare(strict_types=1);

namespace App\ImageProcessing;

use App\Models\ImageProcessingItem;

/**
 * Image Everything (Phase S3 — Image SEO / Smart Metadata Generator).
 *
 * DELIBERATE ARCHITECTURE DECISION: no vision-AI/external API is used
 * anywhere in this class — every field below is built from pure PHP
 * template logic, combining three things this app already has in hand
 * for free: (1) the person's own "Image Context" form answers, (2)
 * the uploaded file's own original filename, and (3) whatever Phase
 * S2's own App\Models\ImageProcessingItem::$width/$height/$format/
 * $file_size_bytes/$metadata already captured. It never looks at
 * actual pixel CONTENT to guess what's "in" the photo — it composes
 * copy around what the PERSON told it the image is for.
 *
 * Every generated string is run through
 * App\ImageProcessing\KeywordStuffingDetector::scanAndFix() before it
 * ever reaches a candidate array — see finalizeCandidate() below — so
 * a caller never has to remember to check for stuffing separately.
 *
 * NOTHING here is ever forced onto the person — generate() only
 * produces CANDIDATES; App\Http\Controllers\ImageSeoController is what
 * decides a 'selected' default (always candidates[0]), and every field
 * stays freely editable/regeneratable from the show page afterward.
 */
final class SmartMetadataGenerator
{
    private const PURPOSE_PHRASES = [
        'product' => ['verb' => 'Shop', 'noun' => 'product photo'],
        'blog' => ['verb' => 'Read about', 'noun' => 'blog visual'],
        'hero' => ['verb' => 'Discover', 'noun' => 'hero image'],
        'infographic' => ['verb' => 'Explore', 'noun' => 'infographic'],
        'screenshot' => ['verb' => 'See', 'noun' => 'screenshot'],
        'logo' => ['verb' => 'Meet', 'noun' => 'logo'],
        'decorative' => ['verb' => 'Enjoy', 'noun' => 'image'],
        'author' => ['verb' => 'Meet', 'noun' => 'author photo'],
        'gallery' => ['verb' => 'Browse', 'noun' => 'gallery photo'],
        'advertisement' => ['verb' => 'Check out', 'noun' => 'advertisement'],
    ];

    public function __construct(
        private readonly KeywordStuffingDetector $stuffingDetector,
    ) {}

    /**
     * @param  array<string, mixed>  $context  the job's own Image Context: primary_keyword, secondary_keywords (list<string>), page_topic, product_name, brand, category, target_audience, purpose (a key from config('image-processing.image_seo.purposes'))
     * @param  int  $variant  0 for the first generation; bumped by ImageSeoController's own regenerate() so re-rolling a field cycles through this class's own template variants instead of repeating the same wording
     * @return array<string, mixed> see this class's own file-level docblock; matches exactly what gets persisted onto App\Models\ImageProcessingItem::$result
     */
    public function generate(ImageProcessingItem $item, array $context, int $variant = 0): array
    {
        $words = $this->contextWords($item, $context);
        $extension = $this->extensionFor($item);

        $filenameText = $this->buildFilename($words, $item, $extension);
        $filenameCandidate = $this->finalizeCandidate($filenameText);

        $titleCandidate = $this->finalizeCandidate($this->buildTitle($words, $context, $variant));
        $captionCandidate = $this->finalizeCandidate($this->buildCaption($words, $context, $variant));
        $descriptionCandidate = $this->finalizeCandidate($this->buildDescription($words, $context, $item, $variant));

        $altStyles = [];

        foreach (array_keys(config('image-processing.image_seo.alt_styles', [])) as $style) {
            $altStyles[$style] = [
                'candidates' => [
                    $this->finalizeCandidate($this->buildAlt($style, $words, $context, $item, $variant + 0)),
                    $this->finalizeCandidate($this->buildAlt($style, $words, $context, $item, $variant + 1)),
                    $this->finalizeCandidate($this->buildAlt($style, $words, $context, $item, $variant + 2)),
                ],
            ];
        }

        return [
            'filename' => [
                'candidates' => [$filenameCandidate],
                'selected' => $filenameCandidate['text'],
            ],
            'title' => [
                'candidates' => [$titleCandidate],
                'selected' => $titleCandidate['text'],
            ],
            'caption' => [
                'candidates' => [$captionCandidate],
                'selected' => $captionCandidate['text'],
            ],
            'description' => [
                'candidates' => [$descriptionCandidate],
                'selected' => $descriptionCandidate['text'],
            ],
            'alt' => [
                'active_style' => 'seo',
                'active_index' => 0,
                'styles' => $altStyles,
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Ordered, deduplicated phrase list built from context fields
     * (falls back to the original filename's own words when the
     * person left every context field blank) — every other build*()
     * method below composes its wording FROM this list, so it's the
     * one place "what is this image actually about" gets decided.
     *
     * @param  array<string, mixed>  $context
     * @return list<string>
     */
    private function contextWords(ImageProcessingItem $item, array $context): array
    {
        $phrases = array_filter([
            trim((string) data_get($context, 'brand', '')),
            trim((string) data_get($context, 'product_name', '')),
            trim((string) data_get($context, 'primary_keyword', '')),
            trim((string) data_get($context, 'category', '')),
        ], static fn (string $p): bool => $p !== '');

        if ($phrases === []) {
            $phrases[] = $this->humanizeFilename($item->original_filename);
        }

        return array_values($phrases);
    }

    private function humanizeFilename(string $filename): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $genericPatterns = config('image-processing.image_seo.generic_filename_patterns', []);

        foreach ($genericPatterns as $pattern) {
            if (preg_match($pattern, $base) === 1) {
                return 'Image';
            }
        }

        $spaced = trim(preg_replace('/[\-_]+/', ' ', $base) ?? $base);

        return $spaced === '' ? 'Image' : ucwords($spaced);
    }

    /**
     * @param  list<string>  $words
     */
    private function buildFilename(array $words, ImageProcessingItem $item, string $extension): string
    {
        $tokens = [];

        foreach ($words as $phrase) {
            foreach (preg_split('/[\s,]+/', $phrase) ?: [] as $word) {
                $clean = strtolower(preg_replace('/[^a-z0-9]/i', '', $word) ?? '');

                if ($clean !== '') {
                    $tokens[] = $clean;
                }
            }
        }

        // Dedupe while preserving first-seen order — "nike-air-nike"
        // never happens even if brand and product name both mention
        // "nike".
        $tokens = array_values(array_unique($tokens));
        $tokens = array_slice($tokens, 0, 8);

        if ($tokens === []) {
            $tokens = ['image'];
        }

        return implode('-', $tokens).'.'.$extension;
    }

    private function extensionFor(ImageProcessingItem $item): string
    {
        $fromOriginal = strtolower(pathinfo($item->original_filename, PATHINFO_EXTENSION));

        if ($fromOriginal !== '') {
            return $fromOriginal;
        }

        return strtolower((string) $item->format) ?: 'jpg';
    }

    /**
     * @param  list<string>  $words
     * @param  array<string, mixed>  $context
     */
    private function buildTitle(array $words, array $context, int $variant): string
    {
        $primary = $this->titleCase($words[0] ?? 'Image');
        $brand = trim((string) data_get($context, 'brand', ''));
        $category = trim((string) data_get($context, 'category', ''));

        $templates = [
            $brand !== '' ? "{$primary} by {$brand}" : $primary,
            $category !== '' ? "{$primary} — {$category}" : $primary,
            $primary,
        ];

        return $templates[$variant % count($templates)];
    }

    /**
     * @param  list<string>  $words
     * @param  array<string, mixed>  $context
     */
    private function buildCaption(array $words, array $context, int $variant): string
    {
        $primary = $this->titleCase($words[0] ?? 'this image');
        $purposeKey = (string) data_get($context, 'purpose', '');
        $phrase = self::PURPOSE_PHRASES[$purposeKey] ?? ['verb' => 'See', 'noun' => 'image'];
        $audience = trim((string) data_get($context, 'target_audience', ''));

        $templates = [
            "{$phrase['verb']} {$primary}.",
            $audience !== '' ? "{$primary} — made for {$audience}." : "{$primary}.",
            "{$phrase['verb']} {$primary} today.",
        ];

        return $templates[$variant % count($templates)];
    }

    /**
     * @param  list<string>  $words
     * @param  array<string, mixed>  $context
     */
    private function buildDescription(array $words, array $context, ImageProcessingItem $item, int $variant): string
    {
        $primary = $this->titleCase($words[0] ?? 'This image');
        $topic = trim((string) data_get($context, 'page_topic', ''));
        $secondary = $this->secondaryKeywordPhrase($context);
        $audience = trim((string) data_get($context, 'target_audience', ''));
        $orientation = $item->width !== null && $item->height !== null
            ? ($item->width >= $item->height ? 'landscape' : 'portrait')
            : null;

        $sentences = [];
        $sentences[] = $topic !== '' ? "{$primary}, shown as part of {$topic}." : "{$primary}.";

        if ($secondary !== '') {
            $sentences[] = "Related to {$secondary}.";
        }

        if ($audience !== '') {
            $sentences[] = "Relevant for {$audience}.";
        } elseif ($orientation !== null && $variant % 2 === 1) {
            $sentences[] = ucfirst($orientation).' orientation, optimized for web display.';
        }

        return implode(' ', $sentences);
    }

    /**
     * @param  list<string>  $words
     * @param  array<string, mixed>  $context
     */
    private function buildAlt(string $style, array $words, array $context, ImageProcessingItem $item, int $variant): string
    {
        $primary = $words[0] ?? $this->humanizeFilename($item->original_filename);
        $purposeKey = (string) data_get($context, 'purpose', '');
        $phrase = self::PURPOSE_PHRASES[$purposeKey] ?? ['verb' => 'See', 'noun' => 'image'];
        $secondary = $this->secondaryKeywordPhrase($context);
        $audience = trim((string) data_get($context, 'target_audience', ''));
        $category = trim((string) data_get($context, 'category', ''));

        return match ($style) {
            'seo' => $this->buildSeoAlt($primary, $category, $secondary, $variant),
            'accessibility' => $this->buildAccessibilityAlt($primary, $phrase['noun'], $item, $variant),
            'short' => $this->buildShortAlt($primary, $variant),
            'detailed' => $this->buildDetailedAlt($primary, $category, $secondary, $audience, $phrase['noun'], $variant),
            default => $this->titleCase($primary),
        };
    }

    private function buildSeoAlt(string $primary, string $category, string $secondary, int $variant): string
    {
        $primary = $this->titleCase($primary);

        $templates = array_values(array_filter([
            $category !== '' ? "{$primary} — {$category}" : null,
            $secondary !== '' ? "{$primary} | {$secondary}" : null,
            $primary,
        ]));

        return $templates[$variant % count($templates)];
    }

    private function buildAccessibilityAlt(string $primary, string $noun, ImageProcessingItem $item, int $variant): string
    {
        $primary = lcfirst($this->titleCase($primary));

        $templates = [
            "A {$noun} showing {$primary}",
            "{$noun} of {$primary}",
            "Image displaying {$primary}, a {$noun}",
        ];

        return ucfirst($templates[$variant % count($templates)]);
    }

    private function buildShortAlt(string $primary, int $variant): string
    {
        $primary = $this->titleCase($primary);
        $words = preg_split('/\s+/', $primary) ?: [$primary];

        $templates = [
            $primary,
            implode(' ', array_slice($words, 0, 3)),
            $primary,
        ];

        return $templates[$variant % count($templates)];
    }

    private function buildDetailedAlt(string $primary, string $category, string $secondary, string $audience, string $noun, int $variant): string
    {
        $primary = $this->titleCase($primary);
        $parts = [];

        if ($category !== '') {
            $parts[] = "in the {$category} category";
        }

        if ($secondary !== '') {
            $parts[] = "featuring {$secondary}";
        }

        if ($audience !== '' && $variant % 2 === 0) {
            $parts[] = "for {$audience}";
        } else {
            $parts[] = "— {$noun}";
        }

        return $parts === [] ? $primary : $primary.' '.implode(', ', $parts);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function secondaryKeywordPhrase(array $context): string
    {
        $secondary = data_get($context, 'secondary_keywords', []);

        if (! is_array($secondary) || $secondary === []) {
            return '';
        }

        return implode(', ', array_slice($secondary, 0, 3));
    }

    private function titleCase(string $phrase): string
    {
        return ucwords(trim($phrase));
    }

    /**
     * Every generated string passes through here — see this class's
     * own file-level docblock. Never mutates the ORIGINAL if nothing
     * was flagged.
     *
     * @return array{text: string, auto_adjusted: bool, source: string, note: ?string}
     */
    private function finalizeCandidate(string $raw): array
    {
        $raw = trim(preg_replace('/\s{2,}/', ' ', $raw) ?? $raw);
        $scan = $this->stuffingDetector->scanAndFix($raw);

        return [
            'text' => $scan['text'],
            'auto_adjusted' => $scan['was_fixed'],
            'source' => 'generated',
            'note' => $scan['was_fixed']
                ? sprintf('Auto-adjusted for repetition — "%s" appeared %d times in the first draft.', $scan['word'], $scan['count'])
                : null,
        ];
    }
}