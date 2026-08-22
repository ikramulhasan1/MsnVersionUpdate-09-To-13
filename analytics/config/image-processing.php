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

    /*
    |--------------------------------------------------------------------------
    | Image SEO / Smart Metadata Generator (Phase S3)
    |--------------------------------------------------------------------------
    |
    | Every tunable knob App\ImageProcessing\SmartMetadataGenerator,
    | App\ImageProcessing\KeywordStuffingDetector,
    | App\ImageProcessing\DuplicateAltDetector, and
    | App\ImageProcessing\ImageSeoScorer use, so tuning any threshold
    | below never means editing those classes themselves — same
    | reasoning as 'quality_analysis' above.
    */
    'image_seo' => [

        // A single job (batch) can't grow unbounded — this caps how
        // many images one "Image Context" submission accepts at once.
        'max_images_per_batch' => 20,

        // The dropdown App\Http\Controllers\ImageSeoController's own
        // store() validates 'purpose' against — kept here (not a PHP
        // enum) since these are purely display/template labels, never
        // switched on with the kind of exhaustive match() an enum
        // would justify elsewhere in this app.
        'purposes' => [
            'product' => 'Product',
            'blog' => 'Blog',
            'hero' => 'Hero',
            'infographic' => 'Infographic',
            'screenshot' => 'Screenshot',
            'logo' => 'Logo',
            'decorative' => 'Decorative',
            'author' => 'Author',
            'gallery' => 'Gallery',
            'advertisement' => 'Advertisement',
        ],

        // Alt text style keys App\ImageProcessing\SmartMetadataGenerator
        // builds three candidates for, and the order they're shown in
        // the UI.
        'alt_styles' => [
            'seo' => 'SEO-focused',
            'accessibility' => 'Accessibility-focused',
            'short' => 'Short',
            'detailed' => 'Detailed',
        ],

        // Recommended alt-text length window (characters) — screen
        // readers and Google both effectively stop reading well past
        // this, so the generator trims toward this range rather than
        // treating "longer is always more thorough" as better.
        'alt_length_min' => 30,
        'alt_length_max' => 125,

        // App\ImageProcessing\KeywordStuffingDetector — a generated
        // text is flagged once its single most-repeated significant
        // word accounts for MORE than this share of all significant
        // words in that text, OR appears at least
        // 'stuffing_min_repeats' times outright (the ratio alone would
        // under-flag very short texts like a Short-style alt where 2
        // repeats of a 4-word phrase already reads as stuffed).
        'stuffing_ratio_threshold' => 0.30,
        'stuffing_min_repeats' => 3,

        // App\ImageProcessing\DuplicateAltDetector — two images' alt
        // text is flagged as a likely duplicate once normalized
        // token-overlap similarity is AT/ABOVE this share (1.0 =
        // identical).
        'duplicate_alt_similarity_threshold' => 0.80,

        // App\ImageProcessing\ImageSeoScorer — the 0-100 checklist
        // score is a straight sum of these four weights (each already
        // expressed directly in points, summing to 100 — unlike
        // 'quality_analysis' above this isn't a 0-1 blend, since the
        // UI shows each criterion's own points directly, not just the
        // final blended number).
        'checklist_weights' => [
            'filename' => 25,
            'alt_text' => 30,
            'context_relevance' => 20,
            'format' => 25,
        ],

        // Formats treated as fully "web-friendly" for the Format
        // criterion above vs. ones that still work but cost points —
        // GIF/BMP are the common "should really be converted" cases in
        // real image libraries.
        'web_friendly_formats' => ['WEBP', 'JPG', 'JPEG', 'PNG'],

        // Generic, non-descriptive filename patterns (case-insensitive
        // regex fragments) the Filename checklist criterion checks the
        // ORIGINAL upload name against — a camera/screenshot default
        // name never became "SEO-friendly" just by sitting next to a
        // keyword-rich generated suggestion the person hasn't actually
        // adopted yet.
        'generic_filename_patterns' => [
            '/^img[_-]?\d+$/i',
            '/^dsc[_-]?\d+$/i',
            '/^image\d*$/i',
            '/^photo\d*$/i',
            '/^screenshot/i',
            '/^untitled/i',
        ],

        // Common English stopwords the generator/detector both strip
        // before counting "significant" words — deliberately small and
        // fixed, not a full linguistic stopword library, since this
        // app's own generated text is short marketing copy, not
        // arbitrary prose.
        'stopwords' => [
            'a', 'an', 'the', 'and', 'or', 'for', 'of', 'in', 'on', 'at',
            'to', 'with', 'by', 'is', 'are', 'this', 'that', 'from',
            'your', 'our', 'it', 'its',
        ],
    ],

];