<?php

declare(strict_types=1);

namespace App\Discovery\Ingestion\DTO;

/**
 * The outcome of one App\Discovery\Ingestion\DiscoveryIngestionService
 * ingest()/discoverAndIngest() call — how many candidate websites
 * turned into real, newly-persisted DiscoveredWebsite rows
 * ($created), and how many were already known and therefore skipped
 * ($skippedExisting, per that service's own deduplication-by-url_hash
 * logic). App\Http\Controllers\DiscoveryController::discover() reads
 * both off this DTO directly to build its "N new website(s)
 * discovered and queued for scoring (M already known)." flash
 * message — see that method's own docblock.
 */
final readonly class IngestionResult
{
    public function __construct(
        public int $created,
        public int $skippedExisting,
    ) {}
}
