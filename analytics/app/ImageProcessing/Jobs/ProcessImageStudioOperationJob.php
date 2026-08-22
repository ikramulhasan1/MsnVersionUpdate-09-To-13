<?php

declare(strict_types=1);

namespace App\ImageProcessing\Jobs;

use App\Enums\ImageItemStatus;
use App\ImageProcessing\Exceptions\ImageStudioException;
use App\ImageProcessing\ImageStudioProcessor;
use App\Models\ImageProcessingOperation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Image Everything (Phase S4) — dispatched once per operation, right
 * when a person submits a resize/crop/compress/convert/responsive
 * request from resources/views/image-studio/show.blade.php. ALWAYS
 * queued, never synchronous — see
 * App\ImageProcessing\ImageStudioProcessor's own docblock for why real
 * Imagick encode/decode work justifies this, same reasoning as Phase
 * S2's own App\ImageProcessing\Jobs\AnalyzeImageMetadataJob.
 *
 * $operation->status is the ONLY thing
 * App\Http\Controllers\ImageStudioController's own poll() endpoint (and
 * the show page's own JS polling loop) needs to watch: PENDING ->
 * PROCESSING (set here, the moment a worker actually picks this job
 * up — not at dispatch time, matching Phase S2's own reasoning for the
 * same distinction) -> COMPLETED or FAILED.
 */
final class ProcessImageStudioOperationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    /** @var array<int, int> */
    public array $backoff = [10, 30];

    public int $timeout = 180;

    public function __construct(
        public readonly ImageProcessingOperation $operation,
    ) {}

    public function handle(ImageStudioProcessor $processor): void
    {
        // Re-fetch rather than trusting the serialized copy blindly —
        // App\Console\Commands\CleanupExpiredImageJobsCommand can
        // delete the whole job (and every operation under it) out from
        // under a queued job that was dispatched just before expiry.
        $operation = $this->operation->fresh();

        if ($operation === null) {
            return;
        }

        $item = $operation->item()->first();

        if ($item === null) {
            return;
        }

        $operation->forceFill(['status' => ImageItemStatus::PROCESSING, 'started_at' => now()])->save();

        try {
            $result = match ($operation->type) {
                'resize' => $processor->resize($item, $operation, $operation->params),
                'crop' => $processor->crop($item, $operation, $operation->params),
                'compress' => $processor->compress($item, $operation, $operation->params),
                'convert' => $processor->convert($item, $operation, $operation->params),
                'responsive' => $processor->responsive($item, $operation, $operation->params),
                default => throw new ImageStudioException("Unknown Image Studio operation type: {$operation->type}"),
            };

            $operation->forceFill([
                'status' => ImageItemStatus::COMPLETED,
                'result' => $result,
                'completed_at' => now(),
            ])->save();
        } catch (ImageStudioException $exception) {
            $this->markFailed($operation, $exception->getMessage());
        }
    }

    /**
     * Only reached once $tries is exhausted — see
     * App\ImageProcessing\Jobs\AnalyzeImageMetadataJob's own failed()
     * for the identical reasoning (guards against double-marking; a
     * plain ImageStudioException from handle() already marks the
     * operation FAILED itself and returns normally).
     */
    public function failed(?Throwable $exception): void
    {
        $operation = $this->operation->fresh();

        if ($operation === null || $operation->status === ImageItemStatus::FAILED) {
            return;
        }

        $this->markFailed($operation, $exception?->getMessage() ?? 'Image processing failed after retrying.');
    }

    private function markFailed(ImageProcessingOperation $operation, string $message): void
    {
        Log::warning('Image Studio operation failed', [
            'operation_id' => $operation->id,
            'item_id' => $operation->item_id,
            'type' => $operation->type,
            'error' => $message,
        ]);

        $operation->forceFill([
            'status' => ImageItemStatus::FAILED,
            'error_message' => mb_substr($message, 0, 1000),
            'completed_at' => now(),
        ])->save();
    }
}