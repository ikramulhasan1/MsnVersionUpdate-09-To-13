<?php

declare(strict_types=1);

namespace App\ImageProcessing;

use App\Models\ImageProcessingItem;

/**
 * Image Everything (Phase S3) — pure PHP, no external API: turns one
 * item's own generated/selected SEO fields plus its job's own Image
 * Context into the 0-100 "Image SEO Checklist/Score" the show page
 * renders, with a green/yellow/red status per criterion (never just
 * the blended total alone — a person needs to know WHICH thing to fix,
 * not just that the score isn't 100). Weights are
 * config('image-processing.image_seo.checklist_weights') and already
 * sum to 100, so the four criterion point values below add up to the
 * final score directly.
 */
final class ImageSeoScorer
{
    /**
     * @param  array<string, mixed>  $context  the job's own Image Context (see App\ImageProcessing\SmartMetadataGenerator's own docblock for its shape)
     * @param  array<string, mixed>  $generated  one item's own $result array (see SmartMetadataGenerator::generate()'s own return shape)
     * @return array{score: int, max: int, criteria: list<array{key: string, label: string, status: string, points: int, max: int, detail: string}>}
     */
    public function score(ImageProcessingItem $item, array $context, array $generated): array
    {
        $weights = config('image-processing.image_seo.checklist_weights', [
            'filename' => 25,
            'alt_text' => 30,
            'context_relevance' => 20,
            'format' => 25,
        ]);

        $criteria = [
            $this->scoreFilename($generated, $context, (int) $weights['filename']),
            $this->scoreAltText($generated, $context, (int) $weights['alt_text']),
            $this->scoreContextRelevance($context, (int) $weights['context_relevance']),
            $this->scoreFormat($item, (int) $weights['format']),
        ];

        return [
            'score' => array_sum(array_column($criteria, 'points')),
            'max' => array_sum(array_column($criteria, 'max')),
            'criteria' => $criteria,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, points: int, max: int, detail: string}
     */
    private function scoreFilename(array $generated, array $context, int $max): array
    {
        $filename = (string) data_get($generated, 'filename.selected', '');
        $ratio = 0.0;
        $notes = [];

        if (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*\.[a-z0-9]{2,5}$/', $filename) === 1) {
            $ratio += 0.4;
        } else {
            $notes[] = 'not in lowercase-hyphenated slug format';
        }

        $primaryKeyword = strtolower(trim((string) data_get($context, 'primary_keyword', '')));

        if ($primaryKeyword !== '' && str_contains(strtolower($filename), str_replace(' ', '-', $primaryKeyword))) {
            $ratio += 0.4;
        } elseif ($primaryKeyword !== '') {
            $notes[] = 'missing the primary keyword';
        } else {
            $ratio += 0.2; // no keyword was ever supplied, so don't fully penalize
        }

        $basename = pathinfo($filename, PATHINFO_FILENAME);

        if ($basename !== '' && strlen($basename) <= 70) {
            $ratio += 0.2;
        } else {
            $notes[] = 'too long for a filename';
        }

        return $this->criterion('filename', 'Filename optimized', $ratio, $max, $notes);
    }

    /**
     * @return array{key: string, label: string, status: string, points: int, max: int, detail: string}
     */
    private function scoreAltText(array $generated, array $context, int $max): array
    {
        $activeStyle = (string) data_get($generated, 'alt.active_style', 'seo');
        $activeIndex = (int) data_get($generated, 'alt.active_index', 0);
        $altText = (string) data_get($generated, "alt.styles.{$activeStyle}.candidates.{$activeIndex}.text", '');

        $ratio = 0.0;
        $notes = [];
        $length = strlen($altText);

        $min = (int) config('image-processing.image_seo.alt_length_min', 30);
        $maxLen = (int) config('image-processing.image_seo.alt_length_max', 125);

        if ($altText === '') {
            $notes[] = 'no alt text selected yet';

            return $this->criterion('alt_text', 'Alt text present & keyword-optimized', 0.0, $max, $notes);
        }

        $ratio += 0.3; // present at all

        if ($length >= $min && $length <= $maxLen) {
            $ratio += 0.3;
        } else {
            $notes[] = $length < $min ? 'shorter than recommended' : 'longer than recommended';
        }

        $primaryKeyword = strtolower(trim((string) data_get($context, 'primary_keyword', '')));

        if ($primaryKeyword !== '' && str_contains(strtolower($altText), $primaryKeyword)) {
            $ratio += 0.25;
        } elseif ($primaryKeyword !== '') {
            $notes[] = 'missing the primary keyword';
        } else {
            $ratio += 0.1;
        }

        $stuffed = (bool) data_get($generated, "alt.styles.{$activeStyle}.candidates.{$activeIndex}.auto_adjusted", false);

        if (! $stuffed) {
            $ratio += 0.15;
        } else {
            $notes[] = 'was auto-adjusted for keyword repetition';
        }

        return $this->criterion('alt_text', 'Alt text present & keyword-optimized', $ratio, $max, $notes);
    }

    /**
     * @return array{key: string, label: string, status: string, points: int, max: int, detail: string}
     */
    private function scoreContextRelevance(array $context, int $max): array
    {
        $fields = ['primary_keyword', 'page_topic', 'product_name', 'brand', 'category', 'target_audience', 'purpose', 'secondary_keywords'];
        $filled = 0;

        foreach ($fields as $field) {
            $value = data_get($context, $field);
            $isFilled = is_array($value) ? $value !== [] : trim((string) $value) !== '';

            if ($isFilled) {
                $filled++;
            }
        }

        $ratio = $filled / count($fields);
        $notes = [];

        if (trim((string) data_get($context, 'primary_keyword', '')) === '') {
            $ratio = min($ratio, 0.4);
            $notes[] = 'no primary keyword was provided';
        }

        if ($notes === []) {
            $notes[] = "{$filled}/" . count($fields) . ' context fields filled in';
        }

        return $this->criterion('context_relevance', 'Context relevant', $ratio, $max, $notes);
    }

    /**
     * @return array{key: string, label: string, status: string, points: int, max: int, detail: string}
     */
    private function scoreFormat(ImageProcessingItem $item, int $max): array
    {
        $format = strtoupper((string) $item->format);
        $webFriendly = in_array($format, config('image-processing.image_seo.web_friendly_formats', []), true);
        $notes = [];

        $ratio = $webFriendly ? 0.7 : 0.2;

        if (! $webFriendly) {
            $notes[] = "{$format} is not an ideal web format — consider converting to WebP or JPEG";
        }

        $sizeBytes = (int) $item->file_size_bytes;

        if ($sizeBytes > 0 && $sizeBytes <= 300_000) {
            $ratio += 0.3;
        } elseif ($sizeBytes <= 1_000_000) {
            $ratio += 0.15;
            $notes[] = 'file size is a bit heavy for the web';
        } else {
            $notes[] = 'file size is large and will slow the page down';
        }

        return $this->criterion('format', 'Format web-friendly', $ratio, $max, $notes);
    }

    /**
     * @param  list<string>  $notes
     * @return array{key: string, label: string, status: string, points: int, max: int, detail: string}
     */
    private function criterion(string $key, string $label, float $ratio, int $max, array $notes): array
    {
        $ratio = max(0.0, min(1.0, $ratio));
        $points = (int) round($ratio * $max);

        $status = match (true) {
            $ratio >= 0.8 => 'green',
            $ratio >= 0.4 => 'yellow',
            default => 'red',
        };

        return [
            'key' => $key,
            'label' => $label,
            'status' => $status,
            'points' => $points,
            'max' => $max,
            'detail' => $notes === [] ? 'Looks good.' : ucfirst(implode('; ', $notes)),
        ];
    }
}