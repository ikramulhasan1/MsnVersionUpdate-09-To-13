<?php

declare(strict_types=1);

namespace App\ImageProcessing;

use App\ImageProcessing\Exceptions\ImageAnalysisException;
use App\Models\ImageProcessingItem;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;
use ImagickPixel;

/**
 * Image Everything (Phase S2) — the ONE place metadata gets extracted
 * and quality gets scored for an uploaded image. Everything here reads
 * the already-validated, already-stored file that Phase S1's own
 * App\ImageProcessing\ImageJobService put on the 'private-images' disk
 * — nothing in this class touches an UploadedFile or does its own
 * upload validation.
 *
 * NO EXTERNAL API, NO AI — every measurement below is a real,
 * deterministic calculation Imagick performs locally against the
 * file's own pixels. See each private method's own docblock for the
 * specific algorithm.
 *
 * EXIF / GPS PRIVACY: GPS EXIF fields ARE extracted into $metadata
 * (not silently dropped) — this app's own requirement was to show a
 * privacy warning when GPS is present, which means the UI needs to
 * know it's there. What this class guarantees instead is that GPS
 * presence is ALSO surfaced as its own explicit boolean
 * ($metadata['exif']['gps']['present']), so a caller that wants to
 * render a warning banner never has to go hunting through raw EXIF
 * keys to figure out whether it should. See
 * App\Models\ImageProcessingItem::hasGpsData() for the accessor built
 * on top of that flag.
 */
final class ImageMetadataExtractor
{
    /**
     * Camera-related EXIF tags this app actually surfaces. Deliberately
     * a fixed whitelist, not "every exif:* key Imagick hands back" —
     * raw EXIF can carry dozens of manufacturer-specific/maker-note
     * fields that are noise for this app's own purposes and would
     * bloat $metadata for no benefit.
     */
    private const CAMERA_EXIF_TAGS = [
        'exif:Make' => 'make',
        'exif:Model' => 'model',
        'exif:LensModel' => 'lens_model',
        'exif:Software' => 'software',
        'exif:DateTimeOriginal' => 'captured_at',
        'exif:ExposureTime' => 'exposure_time',
        'exif:FNumber' => 'f_number',
        // Older EXIF readers/writers use "ISOSpeedRatings"; the
        // current EXIF 2.3+ tag name for the same field (0x8827) is
        // "PhotographicSensitivity" — which one Imagick hands back
        // depends on the ImageMagick/libexif version that wrote or is
        // reading the file, so both are whitelisted and mapped onto
        // the one 'iso' key.
        'exif:ISOSpeedRatings' => 'iso',
        'exif:PhotographicSensitivity' => 'iso',
        'exif:FocalLength' => 'focal_length',
        'exif:FocalLengthIn35mmFilm' => 'focal_length_35mm_equiv',
        'exif:Flash' => 'flash',
        'exif:WhiteBalance' => 'white_balance',
    ];

    private const ORIENTATION_LABELS = [
        Imagick::ORIENTATION_UNDEFINED => 'undefined',
        Imagick::ORIENTATION_TOPLEFT => 'normal',
        Imagick::ORIENTATION_TOPRIGHT => 'flipped_horizontal',
        Imagick::ORIENTATION_BOTTOMRIGHT => 'rotated_180',
        Imagick::ORIENTATION_BOTTOMLEFT => 'flipped_vertical',
        Imagick::ORIENTATION_LEFTTOP => 'transposed',
        Imagick::ORIENTATION_RIGHTTOP => 'rotated_90_cw',
        Imagick::ORIENTATION_RIGHTBOTTOM => 'transverse',
        Imagick::ORIENTATION_LEFTBOTTOM => 'rotated_90_ccw',
    ];

    /**
     * Entry point — App\ImageProcessing\Jobs\AnalyzeImageMetadataJob's own handle() is
     * the only caller. Loads the stored file ONCE, runs both metadata
     * extraction and quality analysis off that same Imagick handle
     * (quality analysis gets its own downscaled/cropped working
     * clones — see below — the ORIGINAL handle is never mutated), then
     * persists both JSON blobs plus the derived quality_score onto the
     * item in a single save.
     *
     * @throws ImageAnalysisException
     */
    public function analyze(ImageProcessingItem $item): void
    {
        $absolutePath = Storage::disk('private-images')->path($item->temp_path);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw new ImageAnalysisException("Stored file is missing or unreadable: {$item->temp_path}");
        }

        $imagick = $this->loadImage($absolutePath);

        try {
            // Animated GIFs/WebPs carry N frames on one Imagick handle —
            // every measurement below is about the image people actually
            // SEE first, so pin to frame 0 rather than accidentally
            // operating on whichever frame Imagick's internal iterator
            // happens to be sitting on.
            $frameCount = $imagick->getNumberImages();
            $imagick->setIteratorIndex(0);

            $metadata = $this->extractMetadata($imagick, $item, $frameCount);
            $format = strtoupper((string) $imagick->getImageFormat());

            $quality = $this->analyzeQuality($imagick, $absolutePath, $format);

            $item->forceFill([
                'metadata' => $metadata,
                'quality_analysis' => $quality,
                'quality_score' => $quality['quality_score'],
                'width' => $metadata['width'],
                'height' => $metadata['height'],
                'format' => $format,
                'analyzed_at' => now(),
            ])->save();
        } catch (ImagickException $exception) {
            throw new ImageAnalysisException(
                "Imagick failed while analyzing {$item->original_filename}: {$exception->getMessage()}",
                previous: $exception,
            );
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    /**
     * @throws ImageAnalysisException
     */
    private function loadImage(string $absolutePath): Imagick
    {
        try {
            $imagick = new Imagick();

            // Belt-and-braces resource caps — this class runs inside a
            // queue worker that may churn through MANY images back to
            // back in the same PHP process (queue:work doesn't restart
            // per job); a single pathological file should not be able
            // to balloon that worker's memory/CPU footprint.
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 256 * 1024 * 1024);
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_MAP, 256 * 1024 * 1024);
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_DISK, 512 * 1024 * 1024);

            $imagick->readImage($absolutePath);

            return $imagick;
        } catch (ImagickException $exception) {
            throw new ImageAnalysisException(
                "Imagick could not read the stored file at {$absolutePath}: {$exception->getMessage()}",
                previous: $exception,
            );
        }
    }

    /**
     * Everything Imagick reads directly off the file — no derived
     * quality scoring here, just facts about the image as it exists.
     */
    private function extractMetadata(Imagick $imagick, ImageProcessingItem $item, int $frameCount): array
    {
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        return [
            'width' => $width,
            'height' => $height,
            'aspect_ratio' => $this->aspectRatio($width, $height),
            'file_size_bytes' => $item->file_size_bytes,
            'mime_type' => $imagick->getImageMimeType(),
            'format' => strtoupper((string) $imagick->getImageFormat()),
            'frame_count' => $frameCount,
            'is_animated' => $frameCount > 1,
            'color_profile' => $this->colorProfile($imagick),
            'resolution' => $this->resolution($imagick),
            'orientation' => self::ORIENTATION_LABELS[$imagick->getImageOrientation()] ?? 'unknown',
            'transparency' => $this->transparency($imagick),
            'exif' => $this->extractExif($imagick),
        ];
    }

    /**
     * Reduces to lowest terms with the GCD rather than returning a raw
     * decimal — "16:9" is what a person expects to see, not "1.777...".
     */
    private function aspectRatio(int $width, int $height): array
    {
        $divisor = $height === 0 ? 1 : $this->greatestCommonDivisor($width, $height);

        return [
            'width' => $width && $divisor ? intdiv($width, $divisor) : $width,
            'height' => $height && $divisor ? intdiv($height, $divisor) : $height,
            'decimal' => $height > 0 ? round($width / $height, 4) : null,
        ];
    }

    private function greatestCommonDivisor(int $a, int $b): int
    {
        while ($b !== 0) {
            [$a, $b] = [$b, $a % $b];
        }

        return max($a, 1);
    }

    private function colorProfile(Imagick $imagick): array
    {
        $profiles = [];

        try {
            // With the $values argument false, this already returns a
            // plain list of profile name strings (e.g. ['icc', 'exif'])
            // — NOT a pattern => data map, so no array_keys() here.
            $profiles = $imagick->getImageProfiles('*', false);
        } catch (ImagickException) {
            // No profile block at all is a completely normal, common
            // case (e.g. most web-optimized JPEGs strip it) — not a
            // failure worth surfacing.
        }

        return [
            'colorspace' => $this->colorspaceLabel($imagick->getImageColorspace()),
            'icc_profile_present' => in_array('icc', $profiles, true),
            'embedded_profiles' => $profiles,
        ];
    }

    private function colorspaceLabel(int $colorspace): string
    {
        static $labels = null;

        if ($labels === null) {
            $labels = [
                Imagick::COLORSPACE_UNDEFINED => 'undefined',
                Imagick::COLORSPACE_RGB => 'rgb',
                Imagick::COLORSPACE_GRAY => 'grayscale',
                Imagick::COLORSPACE_SRGB => 'srgb',
                Imagick::COLORSPACE_CMYK => 'cmyk',
                Imagick::COLORSPACE_CMY => 'cmy',
                Imagick::COLORSPACE_HSL => 'hsl',
                Imagick::COLORSPACE_HSB => 'hsb',
                Imagick::COLORSPACE_LAB => 'lab',
                Imagick::COLORSPACE_YCBCR => 'ycbcr',
            ];
        }

        return $labels[$colorspace] ?? "colorspace_{$colorspace}";
    }

    /**
     * Normalizes to DPI (pixels-per-inch) regardless of the units the
     * file itself declared — a PixelsPerCentimeter file reporting "40"
     * is genuinely 101.6 DPI, not "40 DPI", and comparing/displaying
     * raw un-normalized numbers across files would be misleading.
     */
    private function resolution(Imagick $imagick): array
    {
        $resolution = $imagick->getImageResolution();
        $units = $imagick->getImageUnits();

        $x = (float) ($resolution['x'] ?? 0);
        $y = (float) ($resolution['y'] ?? 0);

        if ($units === Imagick::RESOLUTION_PIXELSPERCENTIMETER) {
            $x *= 2.54;
            $y *= 2.54;
        }

        return [
            'dpi_x' => round($x, 2),
            'dpi_y' => round($y, 2),
            'units_declared' => $units === Imagick::RESOLUTION_UNDEFINED ? 'undefined' : 'normalized_to_dpi',
        ];
    }

    /**
     * Two separate signals, deliberately not collapsed into one
     * boolean: a PNG can carry an alpha CHANNEL while every single
     * pixel in it is fully opaque (e.g. exported from an editor that
     * always keeps alpha around) — that's meaningfully different from
     * an image that actually USES transparency somewhere.
     */
    private function transparency(Imagick $imagick): array
    {
        // Imagick::getImageAlphaChannel() returns a plain bool (does
        // this image carry an alpha channel at all) — it does NOT
        // return one of the ALPHACHANNEL_* constants; those are only
        // ever used as ARGUMENTS to setImageAlphaChannel().
        $hasAlphaChannel = (bool) $imagick->getImageAlphaChannel();
        $actuallyTransparent = false;

        if ($hasAlphaChannel) {
            try {
                $stats = $imagick->getImageChannelMean(Imagick::CHANNEL_ALPHA);
                $quantumRange = $imagick->getQuantumRange();
                $maxAlpha = (float) ($quantumRange['quantumRangeLong'] ?? 0);

                // mean strictly below the fully-opaque max means SOME
                // pixel somewhere is not fully opaque. Both values are
                // read off the SAME raw quantum scale (whatever this
                // Imagick build's own bit depth is — 8/16/32 bit), so
                // no normalization is needed for this comparison.
                $actuallyTransparent = $maxAlpha > 0 && ((float) $stats['mean']) < $maxAlpha;
            } catch (ImagickException) {
                // Leave actuallyTransparent as the conservative false —
                // we know the channel exists, we just couldn't confirm
                // it's actually used.
            }
        }

        return [
            'has_alpha_channel' => $hasAlphaChannel,
            'actually_transparent' => $actuallyTransparent,
        ];
    }

    /**
     * GPS coordinates are converted to decimal degrees (not left as
     * raw DMS rational strings) so a future UI can plot them on a map
     * without re-implementing EXIF rational-number parsing itself —
     * but see this class's own top-level docblock for why the
     * 'present' flag next to them matters just as much as the values.
     */
    private function extractExif(Imagick $imagick): array
    {
        try {
            $properties = $imagick->getImageProperties('exif:*', true);
        } catch (ImagickException) {
            $properties = [];
        }

        $camera = [];

        foreach (self::CAMERA_EXIF_TAGS as $exifKey => $label) {
            if (array_key_exists($exifKey, $properties) && $properties[$exifKey] !== '') {
                $camera[$label] = $properties[$exifKey];
            }
        }

        $latitude = $this->gpsToDecimal(
            $properties['exif:GPSLatitude'] ?? null,
            $properties['exif:GPSLatitudeRef'] ?? null,
        );
        $longitude = $this->gpsToDecimal(
            $properties['exif:GPSLongitude'] ?? null,
            $properties['exif:GPSLongitudeRef'] ?? null,
        );

        return [
            'has_exif' => $properties !== [],
            'camera' => $camera,
            'gps' => [
                'present' => $latitude !== null && $longitude !== null,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
        ];
    }

    /**
     * EXIF GPS coordinates come back from Imagick as a
     * "d/1, m/1, s/100"-style rational-fraction string per component
     * (degrees, minutes, seconds), plus a separate N/S or E/W
     * reference tag for sign. Returns null rather than guessing on
     * anything malformed — a wrong GPS point is worse than a missing
     * one.
     */
    private function gpsToDecimal(?string $dms, ?string $ref): ?float
    {
        if ($dms === null || $ref === null) {
            return null;
        }

        $parts = array_map('trim', explode(',', $dms));

        if (count($parts) !== 3) {
            return null;
        }

        $components = [];

        foreach ($parts as $part) {
            $fraction = array_map('trim', explode('/', $part));

            if (count($fraction) !== 2 || (float) $fraction[1] === 0.0) {
                return null;
            }

            $components[] = ((float) $fraction[0]) / ((float) $fraction[1]);
        }

        [$degrees, $minutes, $seconds] = $components;
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        return in_array(strtoupper($ref), ['S', 'W'], true) ? -$decimal : $decimal;
    }

    /**
     * All four quality metrics plus the final blended quality_score.
     * Deliberately takes the ALREADY-OPEN Imagick handle (not a path)
     * so blur/noise/dynamic-range share one decode of the file — only
     * the compression-artifact check re-reads from disk, and only
     * because it needs pixels at their ORIGINAL, un-resized grid (see
     * calculateCompressionArtifacts()'s own docblock for why).
     */
    private function analyzeQuality(Imagick $imagick, string $absolutePath, string $format): array
    {
        $maxDimension = (int) config('image-processing.quality_analysis.analysis_max_dimension_px', 1600);

        $sourceClone = clone $imagick;
        $sourceClone->setIteratorIndex(0);
        $working = $sourceClone->getImage();
        $sourceClone->clear();
        $sourceClone->destroy();

        if (max($working->getImageWidth(), $working->getImageHeight()) > $maxDimension) {
            // Statistical measurements (variance/std-dev), not
            // pixel-exact ones — trading a little precision for a lot
            // less CPU/memory on large uploads is the right call here.
            $working->resizeImage($maxDimension, $maxDimension, Imagick::FILTER_LANCZOS, 1, true);
        }

        $gray = clone $working;
        $gray->transformImageColorspace(Imagick::COLORSPACE_GRAY);

        try {
            $blur = $this->calculateBlur($gray);
            $noise = $this->calculateNoise($gray);
            $dynamicRange = $this->calculateDynamicRange($gray);
            $compression = $format === 'JPEG' || $format === 'JPG'
                ? $this->calculateCompressionArtifacts($absolutePath)
                : ['applicable' => false, 'blockiness_ratio' => null];
        } finally {
            $gray->clear();
            $gray->destroy();
            $working->clear();
            $working->destroy();
        }

        $qualityScore = $this->calculateQualityScore($blur, $noise, $compression, $dynamicRange);

        return [
            'blur' => $blur,
            'noise' => $noise,
            'compression' => $compression,
            'dynamic_range' => $dynamicRange,
            'quality_score' => $qualityScore,
        ];
    }

    /**
     * Imagick::getImageChannelMean()'s own mean/standardDeviation come
     * back on THIS BUILD's native quantum range — 0-255 on a Q8 build,
     * 0-65535 on a (far more common, distro-packaged) Q16 build, and
     * so on. Every floor/ceiling threshold in config('image-processing
     * .quality_analysis') is calibrated in familiar 8-bit terms (a
     * "sharp" Laplacian variance in the low thousands, not the
     * billions), so every raw standardDeviation this class reads gets
     * scaled down to that same 0-255-equivalent basis via this factor
     * BEFORE it's compared against those thresholds — otherwise the
     * exact same image would score completely differently depending
     * on which quantum depth the server's own libmagickwand happens to
     * be compiled with.
     */
    private function quantumScale(Imagick $imagick): float
    {
        static $scale = null;

        if ($scale === null) {
            $quantumRange = (float) ($imagick->getQuantumRange()['quantumRangeLong'] ?? 255.0);
            $scale = $quantumRange > 0 ? 255.0 / $quantumRange : 1.0;
        }

        return $scale;
    }

    /**
     * Classic "variance of Laplacian" blur detector: convolve a
     * grayscale copy with a 3x3 Laplacian (edge-highlighting) kernel,
     * then measure how much the resulting edge response VARIES across
     * the image. A sharp photo has strong, widely-varying edges after
     * this filter (high variance); a blurry one has weak, nearly-flat
     * edge response everywhere (low variance) — variance is just
     * standard-deviation squared, and Imagick's own
     * getImageChannelMean() gives us that standard deviation directly
     * off the filtered image's own pixel-value histogram.
     */
    private function calculateBlur(Imagick $gray): array
    {
        $laplacian = clone $gray;

        // Standard discrete 3x3 Laplacian kernel.
        $laplacian->convolveImage([0, 1, 0, 1, -4, 1, 0, 1, 0]);

        $stats = $laplacian->getImageChannelMean(Imagick::CHANNEL_GRAY);
        $scaledStdDev = ((float) $stats['standardDeviation']) * $this->quantumScale($laplacian);
        $laplacian->clear();
        $laplacian->destroy();

        $variance = $scaledStdDev ** 2;

        $floor = (float) config('image-processing.quality_analysis.blur_variance_floor', 50.0);
        $ceiling = (float) config('image-processing.quality_analysis.blur_variance_ceiling', 1500.0);

        return [
            'laplacian_variance' => round($variance, 2),
            'sharpness_score' => $this->scaleLinear($variance, $floor, $ceiling),
            'is_likely_blurry' => $variance <= $floor,
        ];
    }

    /**
     * Noise estimate via block-level histogram statistics: the image
     * is divided into a grid of small blocks, and Imagick's own
     * getImageChannelMean() gives the standard deviation of EACH
     * block's own pixel-value histogram. Real photo content (edges,
     * texture) inflates a block's std-dev right along with sensor/
     * compression noise, so this takes the LOWEST-variance blocks
     * (the 20th percentile) as the sample — the smoothest patches of
     * the image are the ones where remaining pixel-to-pixel variation
     * is most likely to be actual noise floor rather than real detail.
     */
    private function calculateNoise(Imagick $gray): array
    {
        $width = $gray->getImageWidth();
        $height = $gray->getImageHeight();
        $blockSize = max(8, (int) config('image-processing.quality_analysis.block_size_px', 16));

        // Cap the grid itself (not just block size) so a very large
        // working image can't still balloon into thousands of
        // crop+stat calls — 12x12 is plenty of samples statistically.
        $cols = max(1, min(12, intdiv($width, $blockSize)));
        $rows = max(1, min(12, intdiv($height, $blockSize)));

        $stepX = intdiv($width, $cols);
        $stepY = intdiv($height, $rows);

        $blockDeviations = [];

        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $block = clone $gray;
                $block->cropImage($stepX, $stepY, $col * $stepX, $row * $stepY);

                $stats = $block->getImageChannelMean(Imagick::CHANNEL_GRAY);
                $blockDeviations[] = ((float) $stats['standardDeviation']) * $this->quantumScale($block);

                $block->clear();
                $block->destroy();
            }
        }

        sort($blockDeviations);
        $sampleCount = max(1, (int) ceil(count($blockDeviations) * 0.2));
        $smoothestBlocks = array_slice($blockDeviations, 0, $sampleCount);
        $noiseLevel = array_sum($smoothestBlocks) / count($smoothestBlocks);

        $floor = (float) config('image-processing.quality_analysis.noise_floor', 1.0);
        $ceiling = (float) config('image-processing.quality_analysis.noise_ceiling', 12.0);

        return [
            'noise_level' => round($noiseLevel, 2),
            'noise_score' => $this->scaleLinear($noiseLevel, $floor, $ceiling, invert: true),
            'blocks_sampled' => count($blockDeviations),
        ];
    }

    /**
     * JPEG stores pixel data as independently-quantized 8x8 DCT
     * blocks, so visible "blockiness" shows up as small brightness
     * discontinuities specifically at multiples of 8 pixels — nowhere
     * else. This crops (never RESIZES — resampling would destroy the
     * very pixel grid this check depends on) a bounded region starting
     * at (0,0), so it lines up with the real block grid, then reads
     * raw grayscale bytes and compares the average pixel-to-pixel jump
     * AT block boundaries against the average jump WITHIN blocks. A
     * ratio near 1.0 means boundaries look like everywhere else (no
     * visible blocking); a ratio well above 1.0 means boundaries are
     * measurably rougher than their surroundings — classic JPEG
     * blockiness.
     */
    private function calculateCompressionArtifacts(string $absolutePath): array
    {
        $sampleSize = (int) config('image-processing.quality_analysis.compression_sample_px', 512);
        // Round down to a multiple of 8 so the sampled region's own
        // right/bottom edge doesn't cut a block in half.
        $sampleSize -= $sampleSize % 8;

        $source = new Imagick();
        $source->setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 128 * 1024 * 1024);
        $source->readImage($absolutePath);
        $source->setIteratorIndex(0);

        $width = min($sampleSize, $source->getImageWidth());
        $height = min($sampleSize, $source->getImageHeight());
        $width -= $width % 8;
        $height -= $height % 8;

        if ($width < 16 || $height < 16) {
            $source->clear();
            $source->destroy();

            return ['applicable' => false, 'blockiness_ratio' => null];
        }

        $source->cropImage($width, $height, 0, 0);
        $source->transformImageColorspace(Imagick::COLORSPACE_GRAY);

        // 'I' (intensity) + PIXEL_CHAR gives back a flat, row-major PHP
        // array of ints 0-255 — one per pixel, NOT a packed byte
        // string, so these are plain array reads below.
        $pixels = $source->exportImagePixels(0, 0, $width, $height, 'I', Imagick::PIXEL_CHAR);
        $source->clear();
        $source->destroy();

        $boundaryDiffs = [];
        $withinBlockDiffs = [];

        // Vertical boundaries: compare the column just left of each
        // 8px line to the column just right of it.
        for ($x = 8; $x < $width; $x += 8) {
            for ($y = 0; $y < $height; $y++) {
                $left = (int) $pixels[$y * $width + ($x - 1)];
                $right = (int) $pixels[$y * $width + $x];
                $boundaryDiffs[] = abs($left - $right);

                $midLeft = (int) $pixels[$y * $width + ($x - 5)];
                $midRight = (int) $pixels[$y * $width + ($x - 4)];
                $withinBlockDiffs[] = abs($midLeft - $midRight);
            }
        }

        // Horizontal boundaries: same idea, comparing rows.
        for ($y = 8; $y < $height; $y += 8) {
            for ($x = 0; $x < $width; $x++) {
                $top = (int) $pixels[($y - 1) * $width + $x];
                $bottom = (int) $pixels[$y * $width + $x];
                $boundaryDiffs[] = abs($top - $bottom);

                $midTop = (int) $pixels[($y - 5) * $width + $x];
                $midBottom = (int) $pixels[($y - 4) * $width + $x];
                $withinBlockDiffs[] = abs($midTop - $midBottom);
            }
        }

        $avgBoundaryDiff = count($boundaryDiffs) > 0 ? array_sum($boundaryDiffs) / count($boundaryDiffs) : 0.0;
        $avgWithinDiff = count($withinBlockDiffs) > 0 ? array_sum($withinBlockDiffs) / count($withinBlockDiffs) : 0.0;

        // A near-flat sampled region (avgWithinDiff ~ 0) would make the
        // ratio explode/undefined for a meaningless reason — treat
        // that as "no measurable blockiness" rather than a false
        // maximum score.
        $ratio = $avgWithinDiff > 0.5 ? $avgBoundaryDiff / $avgWithinDiff : 1.0;

        $floor = (float) config('image-processing.quality_analysis.blockiness_floor', 1.05);
        $ceiling = (float) config('image-processing.quality_analysis.blockiness_ceiling', 3.0);

        return [
            'applicable' => true,
            'blockiness_ratio' => round($ratio, 3),
            'compression_score' => $this->scaleLinear($ratio, $floor, $ceiling, invert: true),
            'sampled_region_px' => ['width' => $width, 'height' => $height],
        ];
    }

    /**
     * Directly implements "min/max spread of the histogram" as its own
     * literal algorithm: walks the grayscale image's real pixel-value
     * histogram (Imagick::getImageHistogram(), one ImagickPixel per
     * distinct value actually present) to find the lowest and highest
     * gray levels that occur at least once, rather than approximating
     * from statistics — a single stray near-black or near-white pixel
     * still counts, exactly as a histogram's own min/max would show.
     */
    private function calculateDynamicRange(Imagick $gray): array
    {
        $histogram = $gray->getImageHistogram();

        $min = 255;
        $max = 0;

        /** @var ImagickPixel $pixel */
        foreach ($histogram as $pixel) {
            $color = $pixel->getColor();
            $value = (int) $color['r'];
            $min = min($min, $value);
            $max = max($max, $value);
        }

        if ($max < $min) {
            // Defensive only — an empty histogram should never happen
            // for a successfully-decoded image.
            $min = 0;
            $max = 0;
        }

        $rangePercent = round((($max - $min) / 255) * 100, 2);

        return [
            'min_value' => $min,
            'max_value' => $max,
            'range_percent' => $rangePercent,
            'dynamic_range_score' => round($rangePercent, 2),
        ];
    }

    /**
     * Simple fixed weighted average of each metric's own 0-100
     * sub-score — see config('image-processing.quality_analysis.weights')
     * for the actual numbers, kept there (not here) so tuning the
     * formula is a config change, not a code change. When compression
     * genuinely doesn't apply (non-JPEG), its own weight is
     * redistributed proportionally across the remaining three rather
     * than silently counting as a zero, which would unfairly punish
     * every PNG/WebP/GIF for a metric that was never relevant to it.
     */
    private function calculateQualityScore(array $blur, array $noise, array $compression, array $dynamicRange): int
    {
        $weights = config('image-processing.quality_analysis.weights', [
            'sharpness' => 0.40,
            'noise' => 0.20,
            'compression' => 0.20,
            'dynamic_range' => 0.20,
        ]);

        $scores = [
            'sharpness' => $blur['sharpness_score'],
            'noise' => $noise['noise_score'],
            'dynamic_range' => $dynamicRange['dynamic_range_score'],
        ];

        $activeWeights = [
            'sharpness' => $weights['sharpness'],
            'noise' => $weights['noise'],
            'dynamic_range' => $weights['dynamic_range'],
        ];

        if ($compression['applicable'] ?? false) {
            $scores['compression'] = $compression['compression_score'];
            $activeWeights['compression'] = $weights['compression'];
        }

        $weightTotal = array_sum($activeWeights);

        $weighted = 0.0;

        foreach ($scores as $metric => $score) {
            $weighted += $score * ($activeWeights[$metric] / $weightTotal);
        }

        return (int) round(max(0, min(100, $weighted)));
    }

    /**
     * Maps a raw measurement onto a 0-100 sub-score by clamping it
     * between the configured floor/ceiling and scaling linearly.
     * $invert flips the direction for metrics where LOWER-is-better
     * (noise, blockiness) instead of higher-is-better (sharpness).
     */
    private function scaleLinear(float $value, float $floor, float $ceiling, bool $invert = false): float
    {
        if ($ceiling <= $floor) {
            return 100.0;
        }

        $clamped = max($floor, min($ceiling, $value));
        $normalized = ($clamped - $floor) / ($ceiling - $floor);

        if ($invert) {
            $normalized = 1 - $normalized;
        }

        return round($normalized * 100, 2);
    }
}
