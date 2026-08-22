<?php

declare(strict_types=1);

namespace App\ImageProcessing;

use App\ImageProcessing\Exceptions\ImageStudioException;
use App\Models\ImageProcessingItem;
use App\Models\ImageProcessingOperation;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;
use ImagickPixel;

/**
 * Image Everything (Phase S4 — Image Studio). DELIBERATE ARCHITECTURE
 * DECISION, same as Phase S3's own SmartMetadataGenerator: no external
 * API anywhere in this class — every operation below is real, local
 * Imagick processing against the file this app already has stored on
 * the 'private-images' disk (see App\ImageProcessing\ImageJobService's
 * own docblock). This app's own server ships Imagick with full WebP
 * and AVIF delegate support, so convert()/responsive() below can
 * target either format directly.
 *
 * Every public method here is deliberately DUMB/MECHANICAL — it
 * receives already-resolved, already-validated final numbers (exact
 * width/height, exact crop rectangle, exact quality, exact target
 * format) and just executes them. Anything resembling a DECISION
 * (which preset maps to which dimensions, a centered crop rectangle
 * for a ratio, a compression/format-savings estimate) lives in
 * App\ImageProcessing\ImageStudioRecommender instead — see that
 * class's own docblock for why "no AI/subject-detection" specifically
 * has to hold there, not here.
 *
 * Every method here is called ONLY from
 * App\ImageProcessing\Jobs\ProcessImageStudioOperationJob — never
 * synchronously from a controller (unlike Phase S3's own
 * SmartMetadataGenerator, actual Imagick encode/decode work is
 * genuinely expensive enough to justify the queue, matching Phase
 * S2's own AnalyzeImageMetadataJob reasoning).
 *
 * OUTPUT FILE LOCATION: every output this class writes lives at
 * "{job-uuid}/processed/op-{operation-id}/{filename}" on the
 * 'private-images' disk — nested under that SAME job's own folder
 * (not a separate top-level location), so
 * App\Console\Commands\CleanupExpiredImageJobsCommand deleting a
 * job's directory already sweeps every operation's output with it.
 */
final class ImageStudioProcessor
{
    /**
     * @param  array{width: ?int, height: ?int, maintain_aspect: bool}  $params
     * @return array<string, mixed>
     *
     * @throws ImageStudioException
     */
    public function resize(ImageProcessingItem $item, ImageProcessingOperation $operation, array $params): array
    {
        $imagick = $this->loadOriginal($item);

        try {
            $width = $params['width'] !== null ? $this->clampDimension((int) $params['width']) : 0;
            $height = $params['height'] !== null ? $this->clampDimension((int) $params['height']) : 0;

            if ($width === 0 && $height === 0) {
                throw new ImageStudioException('Resize requires at least a width or a height.');
            }

            $imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1, (bool) $params['maintain_aspect']);

            return $this->finalize($item, $operation, $imagick, strtoupper((string) $item->format), 'resized');
        } catch (ImagickException $exception) {
            throw new ImageStudioException("Resize failed: {$exception->getMessage()}", previous: $exception);
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    /**
     * Manual/CENTER crop ONLY — see this class's own file-level
     * docblock and App\ImageProcessing\ImageStudioRecommender's own
     * docblock. $params always carries an exact pixel rectangle by the
     * time it reaches here, computed EITHER from the person's own
     * drag-selection (resources/views/image-studio/show.blade.php's
     * own JS) OR from ImageStudioRecommender::cropRectForRatio()'s
     * mathematically centered rectangle for a fixed ratio preset —
     * never anything resembling subject/content detection.
     *
     * @param  array{x: int, y: int, width: int, height: int}  $params
     * @return array<string, mixed>
     *
     * @throws ImageStudioException
     */
    public function crop(ImageProcessingItem $item, ImageProcessingOperation $operation, array $params): array
    {
        $imagick = $this->loadOriginal($item);

        try {
            $sourceWidth = $imagick->getImageWidth();
            $sourceHeight = $imagick->getImageHeight();

            $width = max(1, min((int) $params['width'], $sourceWidth));
            $height = max(1, min((int) $params['height'], $sourceHeight));
            $x = max(0, min((int) $params['x'], $sourceWidth - $width));
            $y = max(0, min((int) $params['y'], $sourceHeight - $height));

            $imagick->cropImage($width, $height, $x, $y);
            // Clears the virtual-canvas offset Imagick otherwise leaves
            // behind after a crop, which some viewers respect and
            // others don't — without this the cropped image can appear
            // to "float" inside its original canvas size in tools that
            // do honor it.
            $imagick->setImagePage(0, 0, 0, 0);

            return $this->finalize($item, $operation, $imagick, strtoupper((string) $item->format), 'cropped');
        } catch (ImagickException $exception) {
            throw new ImageStudioException("Crop failed: {$exception->getMessage()}", previous: $exception);
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    /**
     * @param  array{quality: int}  $params
     * @return array<string, mixed>
     *
     * @throws ImageStudioException
     */
    public function compress(ImageProcessingItem $item, ImageProcessingOperation $operation, array $params): array
    {
        $imagick = $this->loadOriginal($item);

        try {
            $quality = max(10, min(100, (int) $params['quality']));
            $imagick->setImageCompressionQuality($quality);

            return $this->finalize($item, $operation, $imagick, strtoupper((string) $item->format), 'compressed');
        } catch (ImagickException $exception) {
            throw new ImageStudioException("Compression failed: {$exception->getMessage()}", previous: $exception);
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    /**
     * @param  array{format: string, quality: ?int}  $params
     * @return array<string, mixed>
     *
     * @throws ImageStudioException
     */
    public function convert(ImageProcessingItem $item, ImageProcessingOperation $operation, array $params): array
    {
        $imagick = $this->loadOriginal($item);

        try {
            $targetFormat = strtoupper((string) $params['format']);

            // JPEG has no alpha channel — flatten onto white first, or
            // a transparent PNG/WebP converted to JPG would otherwise
            // come out with garbled/black transparent regions.
            if (in_array($targetFormat, ['JPG', 'JPEG'], true) && $imagick->getImageAlphaChannel()) {
                $imagick->setImageBackgroundColor(new ImagickPixel('white'));
                $imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            }

            $imagick->setImageFormat($this->imagickFormatFor($targetFormat));

            if ($params['quality'] !== null) {
                $imagick->setImageCompressionQuality(max(10, min(100, (int) $params['quality'])));
            }

            return $this->finalize($item, $operation, $imagick, $targetFormat, 'converted');
        } catch (ImagickException $exception) {
            throw new ImageStudioException("Format conversion to {$params['format']} failed: {$exception->getMessage()}", previous: $exception);
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    /**
     * Responsive Image Generator — one WebP file per surviving width
     * (a requested width AT OR ABOVE the original's own width is
     * skipped outright; this app never upscales for a responsive set).
     * If every requested width would be skipped that way (the original
     * is smaller than the smallest requested width), the original's
     * own width is used as the sole variant instead of returning
     * nothing.
     *
     * @param  array{widths: list<int>}  $params
     * @return array{variants: list<array{width: int, path: string, file_size_bytes: int}>, srcset_html: string}
     *
     * @throws ImageStudioException
     */
    public function responsive(ImageProcessingItem $item, ImageProcessingOperation $operation, array $params): array
    {
        $original = $this->loadOriginal($item);

        try {
            $originalWidth = $original->getImageWidth();
            $quality = (int) config('image-processing.image_studio.responsive_quality', 82);

            $widths = array_values(array_unique(array_filter(
                array_map(static fn ($w): int => (int) $w, $params['widths']),
                static fn (int $w): bool => $w > 0 && $w < $originalWidth,
            )));

            sort($widths);

            if ($widths === []) {
                $widths = [$originalWidth];
            }

            $variants = [];
            $directory = $this->outputDirectory($item, $operation);

            foreach ($widths as $width) {
                $clone = clone $original;

                try {
                    $clone->resizeImage($width, 0, Imagick::FILTER_LANCZOS, 1, false);
                    $clone->setImageFormat('WEBP');
                    $clone->setImageCompressionQuality($quality);

                    $filename = "{$width}w.webp";
                    $relativePath = "{$directory}/{$filename}";

                    Storage::disk('private-images')->put($relativePath, $clone->getImageBlob());

                    $variants[] = [
                        'width' => $width,
                        'path' => $relativePath,
                        'file_size_bytes' => (int) Storage::disk('private-images')->size($relativePath),
                    ];
                } finally {
                    $clone->clear();
                    $clone->destroy();
                }
            }

            return [
                'variants' => $variants,
                'srcset_html' => $this->buildSrcsetHtml($variants, $item->original_filename),
            ];
        } catch (ImagickException $exception) {
            throw new ImageStudioException("Responsive image generation failed: {$exception->getMessage()}", previous: $exception);
        } finally {
            $original->clear();
            $original->destroy();
        }
    }

    /**
     * @throws ImageStudioException
     */
    private function loadOriginal(ImageProcessingItem $item): Imagick
    {
        $absolutePath = Storage::disk('private-images')->path($item->temp_path);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw new ImageStudioException("Stored file is missing or unreadable: {$item->temp_path}");
        }

        try {
            $imagick = new Imagick();

            $imagick->setResourceLimit(Imagick::RESOURCETYPE_MEMORY, (int) config('image-processing.image_studio.imagick_memory_limit_mb', 256) * 1024 * 1024);
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_MAP, (int) config('image-processing.image_studio.imagick_map_limit_mb', 256) * 1024 * 1024);
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_DISK, (int) config('image-processing.image_studio.imagick_disk_limit_mb', 512) * 1024 * 1024);

            $imagick->readImage($absolutePath);
            $imagick->setIteratorIndex(0);

            // Bakes EXIF orientation into the actual pixels before any
            // resize/crop/compress/convert runs, then clears the
            // now-redundant orientation tag — without this, a
            // sideways-shot photo can come out sideways in viewers that
            // don't honor EXIF orientation themselves.
            $imagick->autoOrientImage();

            return $imagick;
        } catch (ImagickException $exception) {
            throw new ImageStudioException("Imagick could not read the stored file: {$exception->getMessage()}", previous: $exception);
        }
    }

    private function clampDimension(int $value): int
    {
        $max = (int) config('image-processing.image_studio.max_dimension_px', 8000);

        return max(0, min($value, $max));
    }

    /**
     * Shared "write the processed Imagick handle out, measure it, and
     * package the result array" tail end for resize()/crop()/compress()/convert()
     * — responsive() doesn't use this (it writes several files, not
     * one, via its own loop above).
     */
    private function finalize(ImageProcessingItem $item, ImageProcessingOperation $operation, Imagick $imagick, string $format, string $suffix): array
    {
        $extension = $this->extensionForFormat($format);
        $directory = $this->outputDirectory($item, $operation);
        $relativePath = "{$directory}/{$suffix}.{$extension}";

        Storage::disk('private-images')->put($relativePath, $imagick->getImageBlob());

        $newSizeBytes = (int) Storage::disk('private-images')->size($relativePath);
        $originalSizeBytes = (int) $item->file_size_bytes;

        return [
            'path' => $relativePath,
            'width' => $imagick->getImageWidth(),
            'height' => $imagick->getImageHeight(),
            'format' => $format,
            'file_size_bytes' => $newSizeBytes,
            'original_file_size_bytes' => $originalSizeBytes,
            'savings_percent' => $originalSizeBytes > 0
                ? round((($originalSizeBytes - $newSizeBytes) / $originalSizeBytes) * 100, 1)
                : 0.0,
        ];
    }

    private function outputDirectory(ImageProcessingItem $item, ImageProcessingOperation $operation): string
    {
        $jobUuid = $item->job->uuid;

        return "{$jobUuid}/processed/op-{$operation->id}";
    }

    private function imagickFormatFor(string $format): string
    {
        return match ($format) {
            'JPG' => 'JPEG',
            default => $format,
        };
    }

    private function extensionForFormat(string $format): string
    {
        return match ($format) {
            'JPG', 'JPEG' => 'jpg',
            'PNG' => 'png',
            'WEBP' => 'webp',
            'AVIF' => 'avif',
            'GIF' => 'gif',
            default => strtolower($format),
        };
    }

    /**
     * @param  list<array{width: int, path: string, file_size_bytes: int}>  $variants
     */
    private function buildSrcsetHtml(array $variants, string $originalFilename): string
    {
        if ($variants === []) {
            return '';
        }

        $srcset = implode(', ', array_map(
            static fn (array $v): string => '/images/'.basename($v['path']).' '.$v['width'].'w',
            $variants,
        ));

        $largest = end($variants);
        $altText = htmlspecialchars(pathinfo($originalFilename, PATHINFO_FILENAME), ENT_QUOTES);

        return sprintf(
            '<img src="/images/%s" srcset="%s" sizes="(max-width: 768px) 100vw, 768px" alt="%s" loading="lazy">',
            basename($largest['path']),
            $srcset,
            $altText,
        );
    }
}