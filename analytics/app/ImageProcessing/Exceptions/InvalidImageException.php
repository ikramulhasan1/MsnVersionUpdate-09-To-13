<?php

declare(strict_types=1);

namespace App\ImageProcessing\Exceptions;

/**
 * Image Everything (Phase S1) — thrown by
 * App\ImageProcessing\ImageJobService::uploadImage() whenever a file
 * fails any validation step (unsupported type, corrupted, signature
 * mismatch). Every caller must catch this and show the real message
 * to the person — never let it surface as a raw 500.
 */
final class InvalidImageException extends \RuntimeException
{
}