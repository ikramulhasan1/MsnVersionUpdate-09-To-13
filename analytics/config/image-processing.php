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

    /*
    |--------------------------------------------------------------------------
    | Quality Analysis (Phase S2)
    |--------------------------------------------------------------------------
    |
    | Every knob App\ImageProcessing\ImageMetadataExtractor's own
    | analyzeQuality() uses to turn raw Imagick measurements into the
    | final 0-100 quality_score, so tuning the formula later never
    | means editing that class itself.
    |
    | 'weights' must sum to 1.0 — each weight is how much that one
    | metric's own 0-100 sub-score contributes to the final blended
    | score. 'compression' effectively drops out (its own weight is
    | redistributed proportionally across the rest) for any image
    | that ISN'T a JPEG, since block-boundary compression artifacts
    | are a JPEG-specific DCT-encoding phenomenon — see
    | calculateCompressionArtifacts()'s own docblock.
    */
    'quality_analysis' => [
        'weights' => [
            'sharpness' => 0.40,
            'noise' => 0.20,
            'compression' => 0.20,
            'dynamic_range' => 0.20,
        ],

        // Laplacian-variance blur detection: variance AT or BELOW this
        // is treated as fully blurry (sharpness sub-score 0); AT or
        // ABOVE the ceiling is treated as fully sharp (sub-score 100).
        // Values in between are scaled linearly. These two numbers are
        // the well-known rough thresholds for the "variance of
        // Laplacian" method on 8-bit grayscale images — genuinely
        // blurry photos commonly fall under ~100, crisp ones commonly
        // land in the thousands.
        'blur_variance_floor' => 50.0,
        'blur_variance_ceiling' => 1500.0,

        // Noise estimate: standard deviation (0-255 scale) measured
        // across the smoothest blocks of the image (see
        // calculateNoiseEstimate()'s own docblock). AT/BELOW the floor
        // is "clean" (sub-score 100); AT/ABOVE the ceiling is "noisy"
        // (sub-score 0).
        'noise_floor' => 1.0,
        'noise_ceiling' => 12.0,

        // Compression-artifact (JPEG block-boundary) blockiness ratio:
        // AT/BELOW the floor is "no visible blocking" (sub-score 100);
        // AT/ABOVE the ceiling is "heavily blocked" (sub-score 0).
        'blockiness_floor' => 1.05,
        'blockiness_ceiling' => 3.0,

        // How large a region (longest edge, in pixels, rounded down to
        // a multiple of 8) the compression-artifact check reads raw
        // pixels from. Cropped from the file's own ORIGINAL, un-resized
        // pixel grid — never resized — see
        // calculateCompressionArtifacts()'s own docblock for why
        // resizing before this specific check would be wrong.
        'compression_sample_px' => 512,

        // Dynamic range sub-score is just (max-min)/255 as a
        // percentage directly — no floor/ceiling needed, but the
        // sampling grid size for the noise/blockiness block analysis
        // below IS configurable, since a finer grid costs more CPU.
        'block_size_px' => 16,

        // Working images larger than this (longest edge, in pixels)
        // are downscaled onto a throwaway clone before any of the
        // quality metrics run — the metrics themselves are statistical
        // (variance/histograms), not pixel-exact, so this trades a
        // little precision for a lot of CPU/memory headroom on huge
        // uploads. The ORIGINAL is never touched or overwritten.
        'analysis_max_dimension_px' => 1600,
    ],

];