<?php

declare(strict_types=1);

/**
 * Image Everything (Phase S1) — every Image Everything phase (S1
 * through S6) reads its own tunable settings from here rather than
 * hard-coding them, so an Admin (or a future .env-based override)
 * can adjust behavior without touching code.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Job Expiry
    |--------------------------------------------------------------------------
    |
    | How long an image_processing_jobs row (and its own real files on
    | the 'private-images' disk) lives before
    | App\Console\Commands\CleanupExpiredImageJobsCommand deletes it.
    | This app's own explicit requirement was "১ ঘণ্টা অথবা ২ ঘণ্টার
    | মতো" — 2 hours is the default, configurable here rather than a
    | magic number buried in App\ImageProcessing\ImageJobService.
    */
    'job_ttl_hours' => (int) env('IMAGE_JOB_TTL_HOURS', 2),

    /*
    |--------------------------------------------------------------------------
    | Abandoned Job Cleanup
    |--------------------------------------------------------------------------
    |
    | A job stuck in 'pending' or 'failed' with no real activity for
    | this long is treated as abandoned and cleaned up EARLY (before
    | its own job_ttl_hours would otherwise expire it) — see
    | App\Console\Commands\CleanupExpiredImageJobsCommand's own
    | docblock for why this exists as a SEPARATE, shorter window than
    | the normal TTL above.
    */
    'abandoned_after_minutes' => (int) env('IMAGE_JOB_ABANDONED_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Allowed Image Types
    |--------------------------------------------------------------------------
    |
    | Both the MIME type AND the file's own magic-byte signature (see
    | App\ImageProcessing\ImageJobService::verifyFileSignature()) must
    | match one of these before an upload is accepted — never just one
    | check alone. Keyed by MIME type; each value is the expected magic
    | byte prefix as a hex string.
    */
    'allowed_types' => [
        'image/jpeg' => 'ffd8ff',
        'image/png' => '89504e470d0a1a0a',
        'image/webp' => '52494646', // RIFF — see the signature check's own docblock for the WEBP-specific second check this needs.
        'image/gif' => '474946',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Upload Limits
    |--------------------------------------------------------------------------
    |
    | Fallback limits used when a user has no Plan (or their Plan sets
    | no explicit override — see Phase S6's own
    | App\Models\Plan::maxImageFileSizeMb()/etc for the real per-plan
    | values). Deliberately conservative defaults, not this app's own
    | real server limits (2048M) — those server-level limits exist to
    | make LARGE values POSSIBLE, not to imply every plan should
    | actually allow uploads that big.
    */
    'default_max_file_size_mb' => 10,

];