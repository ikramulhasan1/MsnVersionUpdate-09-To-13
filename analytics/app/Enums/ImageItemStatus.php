<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Image Everything (Phase S1) — App\Models\ImageProcessingItem's own
 * $status column. Deliberately covers the WHOLE pipeline an item can
 * pass through across every later Image Everything phase (S2's own
 * metadata/quality analysis, S3's own SEO metadata generation, S4's
 * own resize/compress/convert) rather than only the handful of cases
 * Phase S1 itself sets — avoids repeatedly extending this enum (and
 * every match() that switches on it) as each later phase ships,
 * matching the lesson already learned from
 * App\Enums\KeywordCapability's own two-round extension history
 * earlier in this app. Phase S1 itself only ever sets PENDING,
 * UPLOADED, or FAILED; the remaining cases exist for later phases to
 * use without another migration/enum change.
 */
enum ImageItemStatus: string
{
    case PENDING = 'pending';
    case UPLOADED = 'uploaded';
    case ANALYZING = 'analyzing';
    case ANALYZED = 'analyzed';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::UPLOADED => 'Uploaded',
            self::ANALYZING => 'Analyzing',
            self::ANALYZED => 'Analyzed',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED], true);
    }
}