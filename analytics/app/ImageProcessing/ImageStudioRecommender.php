<?php

declare(strict_types=1);

namespace App\ImageProcessing;

/**
 * Image Everything (Phase S4). EVERYTHING in this class is a fixed
 * lookup table or a plain arithmetic formula — nothing here ever
 * inspects pixel CONTENT, and nothing here is "AI" in any sense. Three
 * specific things this app's own requirement calls out as NOT allowed,
 * and this class deliberately never does:
 *
 *   1. "Smart Resize for..." dimensions are a flat key=>{width,height}
 *      table (config('image-processing.image_studio.smart_resize_presets'))
 *      — picking "Product Image" always returns the exact same
 *      1200x1200 regardless of what's actually in the photo.
 *   2. Crop is never "smart"/subject-aware — cropRectForRatio() below
 *      only ever returns a mathematically CENTERED rectangle. Actual
 *      pixel cropping happens in App\ImageProcessing\ImageStudioProcessor,
 *      which accepts either that centered rectangle or a person's own
 *      manually-dragged coordinates — nothing in between.
 *   3. Compression/format-savings numbers are ESTIMATES from a fixed
 *      calibration table, shown to set expectations before a real
 *      operation runs — never a measurement of the specific file, and
 *      never presented as exact.
 */
final class ImageStudioRecommender
{
    /**
     * "Smart Resize for..." — a pure lookup, see this class's own
     * docblock point 1.
     *
     * @return array{width: int, height: int}|null
     */
    public function smartResizeDimensions(string $presetKey): ?array
    {
        $preset = config("image-processing.image_studio.smart_resize_presets.{$presetKey}");

        if ($preset === null) {
            return null;
        }

        return ['width' => (int) $preset['width'], 'height' => (int) $preset['height']];
    }

    public function qualityForMode(string $mode): int
    {
        return (int) config("image-processing.image_studio.compression_modes.{$mode}.quality", 75);
    }

    /**
     * Live "before/after" size shown as the Quality slider moves — a
     * straight-line interpolation between this format's own two
     * calibration points (config('image-processing.image_studio.compression_estimate_curve')),
     * NEVER a real re-encode. The genuine size only exists once
     * App\ImageProcessing\ImageStudioProcessor::compress() actually
     * runs, via a queued App\ImageProcessing\Jobs\ProcessImageStudioOperationJob.
     */
    public function estimateCompressedSize(int $originalBytes, string $format, int $quality): int
    {
        $curve = config("image-processing.image_studio.compression_estimate_curve.{$format}")
            ?? ['retain_at_100' => 0.90, 'retain_at_10' => 0.15];

        $quality = max(10, min(100, $quality));

        // Linear interpolation between the two calibration points.
        $t = ($quality - 10) / 90;
        $retain = $curve['retain_at_10'] + $t * ($curve['retain_at_100'] - $curve['retain_at_10']);

        return (int) round($originalBytes * max(0.01, min(1.0, $retain)));
    }

    /**
     * Format Conversion panel's recommendation badge — a fixed
     * FROM=>{TO, estimated savings %} table, see this class's own
     * docblock point 3.
     *
     * @return array{to: string, savings_percent: int}|null
     */
    public function recommendFormat(string $currentFormat): ?array
    {
        $entry = config('image-processing.image_studio.format_conversion_savings_estimate.'.strtoupper($currentFormat));

        if ($entry === null) {
            return null;
        }

        return ['to' => $entry['to'], 'savings_percent' => (int) $entry['savings_percent']];
    }

    /**
     * A mathematically CENTERED rectangle for a fixed aspect ratio —
     * see this class's own docblock point 2. Returns null for the
     * 'free' key (no ratio constraint — the person drags their own
     * rectangle instead) or an unknown key.
     *
     * @return array{x: int, y: int, width: int, height: int}|null
     */
    public function cropRectForRatio(int $originalWidth, int $originalHeight, string $ratioKey): ?array
    {
        $ratio = config("image-processing.image_studio.crop_ratios.{$ratioKey}");

        if (! is_array($ratio) || count($ratio) !== 2) {
            return null;
        }

        [$ratioW, $ratioH] = $ratio;
        $targetRatio = $ratioW / $ratioH;
        $currentRatio = $originalWidth / max(1, $originalHeight);

        if ($currentRatio > $targetRatio) {
            // Original is relatively wider than the target — crop the sides.
            $height = $originalHeight;
            $width = (int) round($height * $targetRatio);
        } else {
            // Original is relatively taller than the target — crop top/bottom.
            $width = $originalWidth;
            $height = (int) round($width / $targetRatio);
        }

        $width = min($width, $originalWidth);
        $height = min($height, $originalHeight);

        return [
            'x' => (int) round(($originalWidth - $width) / 2),
            'y' => (int) round(($originalHeight - $height) / 2),
            'width' => $width,
            'height' => $height,
        ];
    }
}