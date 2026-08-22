<?php

declare(strict_types=1);

namespace App\ImageProcessing\Jobs;

use App\Enums\ImageItemStatus;
use App\ImageProcessing\Exceptions\ImageAnalysisException;
use App\ImageProcessing\ImageMetadataExtractor;
use App\Models\ImageProcessingItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Image Everything (Phase S2) — dispatched from
 * App\ImageProcessing\ImageJobService::uploadImage() the moment a
 * single image finishes uploading. ALWAYS queued, never run
 * synchronously inline with the upload request — a bulk upload can
 * mean dozens of images landing in the same request/short window, and
 * making the person's browser wait on Imagick analysis for every one
 * of them before the upload response even returns would be a terrible
 * experience (and, on shared hosting, a good way to hit a PHP request
 * timeout).
 *
 * $item->status is the ONLY thing a caller (or the UI polling a job's
 * own progress) needs to watch: UPLOADED -> ANALYZING (set here, the
 * moment this job actually starts running, NOT at dispatch time — a
 * job can sit in the queue for a while under load, and "analyzing"
 * should mean a worker is genuinely working on it right now) ->
 * ANALYZED or FAILED.
 */
final class AnalyzeImageMetadataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    /** @var array<int, int> */
    public array $backoff = [10, 30];

    public int $timeout = 90;

    public function __construct(
        public readonly ImageProcessingItem $item,
    ) {
    }

    public function handle(ImageMetadataExtractor $extractor): void
    {
        // Re-fetch rather than trusting the serialized copy blindly —
        // App\Console\Commands\CleanupExpiredImageJobsCommand can
        // delete an item (and its own files) out from under a queued
        // job if a job's own expires_at was already very close when
        // this was dispatched. A gone item/file is not a failure worth
        // retrying over, just nothing left to do.
        $item = $this->item->fresh();

        if ($item === null) {
            return;
        }

        $item->forceFill(['status' => ImageItemStatus::ANALYZING])->save();

        try {
            $extractor->analyze($item);

            $item->forceFill(['status' => ImageItemStatus::ANALYZED])->save();
        } catch (ImageAnalysisException $exception) {
            $this->markFailed($item, $exception->getMessage());
        }
    }

    /**
     * Only reached once $tries is exhausted (both attempts threw) —
     * Laravel calls this instead of letting the exception bubble to
     * the worker as a raw crash. Guards against double-marking: a
     * plain ImageAnalysisException from handle() already marks the
     * item FAILED itself and returns normally (no exception escapes
     * handle() for that case), so this only fires for something truly
     * unexpected (e.g. a database error while saving).
     */
    public function failed(?Throwable $exception): void
    {
        $item = $this->item->fresh();

        if ($item === null || $item->status === ImageItemStatus::FAILED) {
            return;
        }

        $this->markFailed($item, $exception?->getMessage() ?? 'Image analysis failed after retrying.');
    }

    private function markFailed(ImageProcessingItem $item, string $message): void
    {
        Log::warning('Image metadata analysis failed', [
            'item_id' => $item->id,
            'job_id' => $item->job_id,
            'error' => $message,
        ]);

        $item->forceFill([
            'status' => ImageItemStatus::FAILED,
            'error_message' => mb_substr($message, 0, 1000),
        ])->save();
    }
}
