<?php

declare(strict_types=1);

namespace App\ImageProcessing\Exceptions;

/**
 * Image Everything (Phase S2) — thrown by
 * App\ImageProcessing\ImageMetadataExtractor whenever Imagick itself
 * can't load or analyze a stored file (corrupted beyond what Phase
 * S1's own upload-time checks could catch, an Imagick resource limit
 * hit, an unreadable temp_path because a job's own cleanup already
 * ran, etc). App\ImageProcessing\Jobs\AnalyzeImageMetadataJob is the only catcher —
 * it turns this into the item's own $status = FAILED and
 * $error_message, never lets it surface as a raw queue-worker crash.
 */
final class ImageAnalysisException extends \RuntimeException
{
}
