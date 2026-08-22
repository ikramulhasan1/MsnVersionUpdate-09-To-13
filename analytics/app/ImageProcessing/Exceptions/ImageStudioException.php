<?php

declare(strict_types=1);

namespace App\ImageProcessing\Exceptions;

/**
 * Image Everything (Phase S4) — thrown by
 * App\ImageProcessing\ImageStudioProcessor whenever Imagick can't
 * perform a requested resize/crop/compress/convert/responsive
 * operation (bad/out-of-range params, an unreadable source file, an
 * Imagick resource limit hit, an unsupported output format on this
 * server's own Imagick build). App\ImageProcessing\Jobs\ProcessImageStudioOperationJob
 * is the only catcher — it turns this into the operation's own
 * $status = FAILED and $error_message, never lets it surface as a raw
 * queue-worker crash.
 */
final class ImageStudioException extends \RuntimeException
{
}