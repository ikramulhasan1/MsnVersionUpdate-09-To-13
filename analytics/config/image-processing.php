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

    /*
    |--------------------------------------------------------------------------
    | Image Studio — Resize / Compress / Convert (Phase S4)
    |--------------------------------------------------------------------------
    |
    | Every tunable knob App\ImageProcessing\ImageStudioProcessor (the
    | actual Imagick engine) and App\ImageProcessing\ImageStudioRecommender
    | (pure heuristic mapping — NEVER AI/subject-detection, see that
    | class's own docblock) use.
    */
    'image_studio' => [

        // A single batch can't grow unbounded, same reasoning as
        // Phase S3's own 'image_seo.max_images_per_batch'.
        'max_images_per_batch' => 20,

        // Preset widths (px) the Resize panel offers as one-click
        // buttons, plus a dedicated small "Thumbnail" preset — both
        // App\ImageProcessing\ImageStudioProcessor::resize() and the
        // resources/views/image-studio/show.blade.php UI read this
        // same list so the buttons never drift out of sync with what
        // the backend actually accepts.
        'resize_presets' => [1920, 1600, 1200, 1024, 800, 600, 400],
        'thumbnail_width' => 200,

        // "Smart Resize for..." — App\ImageProcessing\ImageStudioRecommender
        // ONLY task is looking a key up in this fixed table below and
        // handing back its width/height; it is NEVER shown pixel
        // content and NEVER detects a "subject" in the photo. Purely a
        // pre-defined rule/mapping, exactly as this app's own
        // requirement specifies.
        'smart_resize_presets' => [
            'blog_featured' => ['label' => 'Blog Featured Image', 'width' => 1200, 'height' => 675],
            'product' => ['label' => 'Product Image', 'width' => 1200, 'height' => 1200],
            'hero_banner' => ['label' => 'Hero Banner', 'width' => 1920, 'height' => 1080],
            'social_share' => ['label' => 'Social Share (OG)', 'width' => 1200, 'height' => 630],
            'thumbnail' => ['label' => 'Thumbnail', 'width' => 200, 'height' => 200],
            'avatar' => ['label' => 'Avatar / Profile Photo', 'width' => 400, 'height' => 400],
        ],

        // Crop panel — FIXED ratio presets only, plus "Free". This app's
        // own explicit requirement: manual or CENTER crop only, no
        // "smart"/AI subject detection claimed anywhere — see
        // App\ImageProcessing\ImageStudioProcessor::crop()'s own
        // docblock.
        'crop_ratios' => [
            'free' => null,
            '1:1' => [1, 1],
            '4:3' => [4, 3],
            '16:9' => [16, 9],
            '3:2' => [3, 2],
            '9:16' => [9, 16],
        ],

        // Compression mode presets — App\ImageProcessing\ImageStudioRecommender::qualityForMode()
        // is the ONLY place these numbers are read; the Quality slider
        // (10-100) always wins when a person adjusts it directly after
        // picking a mode.
        'compression_modes' => [
            'lossless' => ['label' => 'Lossless', 'quality' => 100],
            'high_quality' => ['label' => 'High Quality', 'quality' => 90],
            'balanced' => ['label' => 'Balanced', 'quality' => 75],
            'maximum_compression' => ['label' => 'Maximum Compression', 'quality' => 40],
        ],

        // App\ImageProcessing\ImageStudioRecommender::estimateCompressedSize()
        // — a PURE ARITHMETIC ESTIMATE (never a real re-encode) used for
        // the compression panel's "live" before/after preview as the
        // quality slider moves; the REAL size only exists once the
        // queued operation actually finishes. Each format's own curve
        // is modeled as roughly linear between these two calibration
        // points (quality 100 keeps 'retain_at_100' of the original
        // size; quality 10 keeps 'retain_at_10').
        'compression_estimate_curve' => [
            'JPG' => ['retain_at_100' => 0.95, 'retain_at_10' => 0.08],
            'JPEG' => ['retain_at_100' => 0.95, 'retain_at_10' => 0.08],
            'PNG' => ['retain_at_100' => 0.90, 'retain_at_10' => 0.20],
            'WEBP' => ['retain_at_100' => 0.85, 'retain_at_10' => 0.05],
            'AVIF' => ['retain_at_100' => 0.70, 'retain_at_10' => 0.03],
            'GIF' => ['retain_at_100' => 0.98, 'retain_at_10' => 0.35],
        ],

        // Format Conversion panel — App\ImageProcessing\ImageStudioRecommender::recommendFormat()
        // is a SIMPLE HEURISTIC only (this app's own requirement:
        // "একটা simple heuristic দিয়ে") — an expected-savings percent
        // per FROM=>TO pair, not a measurement of this specific file.
        'formats' => ['JPG', 'PNG', 'WEBP', 'AVIF'],
        'format_conversion_savings_estimate' => [
            'PNG' => ['to' => 'WEBP', 'savings_percent' => 35],
            'JPG' => ['to' => 'WEBP', 'savings_percent' => 25],
            'JPEG' => ['to' => 'WEBP', 'savings_percent' => 25],
            'WEBP' => ['to' => 'AVIF', 'savings_percent' => 20],
            'GIF' => ['to' => 'WEBP', 'savings_percent' => 40],
        ],

        // Responsive Image Generator — one WebP variant is produced at
        // each of these widths (an original narrower than a given
        // width is simply skipped for that width, never upscaled). See
        // App\ImageProcessing\ImageStudioProcessor::responsive()'s own
        // docblock.
        'responsive_widths' => [400, 800, 1200, 1600, 2000],
        'responsive_format' => 'WEBP',
        'responsive_quality' => 82,

        // Belt-and-braces Imagick resource caps, same reasoning as
        // App\ImageProcessing\ImageMetadataExtractor's own loadImage()
        // — this app's own queue worker processes many images back to
        // back in the same PHP process.
        'imagick_memory_limit_mb' => 256,
        'imagick_map_limit_mb' => 256,
        'imagick_disk_limit_mb' => 512,

        // A hard ceiling on requested resize/crop dimensions — refuses
        // an absurd width/height (whether typed by mistake or crafted
        // deliberately) before it ever reaches Imagick, rather than
        // letting a single operation balloon memory/CPU on the queue
        // worker.
        'max_dimension_px' => 8000,
    ],

];