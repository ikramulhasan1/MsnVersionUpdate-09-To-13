<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Crawler Defaults
    |--------------------------------------------------------------------------
    |
    | Consumed starting in the Crawler Engine phase. Defined now so the
    | config surface (and .env keys) are stable from Phase 1 onward.
    |
    */
    'crawler' => [
        'max_depth' => (int) env('AUDIT_MAX_DEPTH', 2),
        'max_pages' => (int) env('AUDIT_MAX_PAGES', 25),
        'timeout' => (int) env('AUDIT_TIMEOUT', 15),
        'user_agent' => env('AUDIT_USER_AGENT', 'WebsiteAuditBot/1.0 (+https://example.com/bot)'),
        'follow_redirects' => (bool) env('AUDIT_FOLLOW_REDIRECTS', true),
        'respect_robots_txt' => (bool) env('AUDIT_RESPECT_ROBOTS_TXT', true),

        /*
        | Whether the crawler runs a lightweight reachability check
        | (HEAD, falling back to GET) against external links and internal
        | links it discovers but doesn't fully crawl. Disable to speed up
        | crawls on large sites when broken-link detection for those isn't
        | needed.
        */
        'check_external_links' => (bool) env('AUDIT_CHECK_EXTERNAL_LINKS', true),

        /*
        | How many pages (or external-link reachability checks) are
        | fetched concurrently. Pages within the same crawl wave are
        | independent HTTP requests, so raising this shortens total
        | crawl time roughly proportionally, at the cost of hitting the
        | target site with more simultaneous requests. 8 is a polite
        | default for most sites; lower it for crawls against sites
        | known to rate-limit aggressively.
        */
        'concurrency' => (int) env('AUDIT_CRAWLER_CONCURRENCY', 8),
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Validator
    |--------------------------------------------------------------------------
    |
    | Used by App\Audit\Validation\UrlValidatorService for the HTTP reachability
    | and SSL probes. Kept separate from the crawler timeout since validation
    | should fail fast even if crawling is configured to be more patient.
    |
    */
    'validation' => [
        'timeout' => (int) env('AUDIT_VALIDATION_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Analyzer
    |--------------------------------------------------------------------------
    |
    | Thresholds used by App\Audit\Seo\SeoAnalyzerService. The title/
    | description ranges follow common search-result truncation guidance;
    | thin_content_word_count is the body word count below which a page is
    | flagged as thin.
    |
    */
    'seo' => [
        'title_min_length' => (int) env('AUDIT_SEO_TITLE_MIN', 30),
        'title_max_length' => (int) env('AUDIT_SEO_TITLE_MAX', 60),
        'description_min_length' => (int) env('AUDIT_SEO_DESCRIPTION_MIN', 70),
        'description_max_length' => (int) env('AUDIT_SEO_DESCRIPTION_MAX', 160),
        'thin_content_word_count' => (int) env('AUDIT_SEO_THIN_CONTENT_WORDS', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Analyzer
    |--------------------------------------------------------------------------
    |
    | Consumed by App\Audit\Jobs\AnalyzeChunkJob when it fans SecurityAnalyzer
    | out across every successfully crawled page (see
    | SecurityAnalyzer::analyzeAll()) instead of just the entry page.
    | per_page_limit caps how many of those pages actually get fetched
    | and checked, since each one is its own HTTP request on top of the
    | crawl that already happened — without a cap, a very large site
    | could turn a single AnalyzeChunkJob run into dozens of extra
    | requests. Pages beyond the limit are simply not analyzed for
    | security (in crawl order, so the entry page and pages closest to
    | it are always included); this does not affect audit.crawler.max_pages,
    | which controls how many pages are crawled in the first place.
    |
    */
    'security' => [
        'per_page_limit' => (int) env('AUDIT_SECURITY_PER_PAGE_LIMIT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Duplicate Prevention
    |--------------------------------------------------------------------------
    */
    'prevent_duplicate_pending_minutes' => (int) env('AUDIT_DUPLICATE_WINDOW_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Queue Pipeline
    |--------------------------------------------------------------------------
    |
    | Consumed by App\Audit\Jobs\AuditJob and its subclasses
    | (FetchAndCrawlJob, AnalyzeChunkJob, AssembleAnalysisResultsJob).
    |
    */
    'queue' => [
        // Attempts allowed per job before it's considered permanently failed.
        'tries' => (int) env('AUDIT_QUEUE_TRIES', 3),

        // Delay (seconds) before each retry attempt; one entry per retry,
        // last value reused for any attempt beyond the list's length.
        'backoff_seconds' => [10, 30, 90],

        // How many of AnalyzeChunkJob::ANALYZER_KEYS each AnalyzeChunkJob
        // instance runs — bounds memory per worker and controls how many
        // chunks run in parallel within the batch.
        'chunk_size' => (int) env('AUDIT_QUEUE_CHUNK_SIZE', 3),

        // How long a WithoutOverlapping lock is held before it's
        // considered stale and released, in case a worker dies mid-job.
        // Must stay comfortably above every *_timeout_seconds value
        // below, or a legitimately still-running job could have its
        // lock expire and be treated as free to run again.
        'overlap_lock_seconds' => (int) env('AUDIT_QUEUE_OVERLAP_LOCK_SECONDS', 600),

        // How long a ShouldBeUnique lock blocks a duplicate dispatch of
        // the same job for the same audit, in seconds.
        'unique_for_seconds' => (int) env('AUDIT_QUEUE_UNIQUE_FOR_SECONDS', 3600),

        // Fallback for any job that doesn't declare its own timeout.
        'default_timeout_seconds' => (int) env('AUDIT_QUEUE_DEFAULT_TIMEOUT_SECONDS', 120),

        // FetchAndCrawlJob: real HTTP work across up to max_pages pages,
        // each allowed up to audit.crawler.timeout seconds — needs the
        // most headroom of any job in the pipeline.
        'fetch_and_crawl_timeout_seconds' => (int) env('AUDIT_QUEUE_FETCH_AND_CRAWL_TIMEOUT_SECONDS', 300),

        // AnalyzeChunkJob: runs a bounded number of analyzers against an
        // already-cached FetchResult/CrawlResult — no new network I/O
        // in the common case.
        'analyze_chunk_timeout_seconds' => (int) env('AUDIT_QUEUE_ANALYZE_CHUNK_TIMEOUT_SECONDS', 90),

        // AssembleAnalysisResultsJob: cache reads + one DB write only.
        'assemble_timeout_seconds' => (int) env('AUDIT_QUEUE_ASSEMBLE_TIMEOUT_SECONDS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Cache TTLs
    |--------------------------------------------------------------------------
    |
    | Consumed by App\Audit\Cache\AuditCacheService (bound with these
    | values in AuditServiceProvider). Fetch/crawl results are cached
    | per-URL and reused across audits; analysis results are cached
    | per-audit-uuid until the API/PDF export layer reads them.
    |
    */
    'cache' => [
        'fetch_ttl_seconds' => (int) env('AUDIT_CACHE_FETCH_TTL_SECONDS', 3600),
        'crawl_ttl_seconds' => (int) env('AUDIT_CACHE_CRAWL_TTL_SECONDS', 3600),
        'results_ttl_seconds' => (int) env('AUDIT_CACHE_RESULTS_TTL_SECONDS', 86400),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Export
    |--------------------------------------------------------------------------
    |
    | Used by App\Audit\Export\Pdf\AuditPdfExportService (via
    | AuditServiceProvider) and App\Audit\Export\Pdf\Support\AuditToPdfHeaderData.
    | logo_path is relative to the public/ directory; if no file exists there,
    | the PDF header falls back to a text company mark instead of a logo image.
    |
    */
    'pdf' => [
        'paper_size' => env('AUDIT_PDF_PAPER_SIZE', 'a4'),
        'orientation' => env('AUDIT_PDF_ORIENTATION', 'portrait'),
        'logo_path' => env('AUDIT_PDF_LOGO_PATH', 'images/logo.png'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PageSpeed Insights (Core Web Vitals)
    |--------------------------------------------------------------------------
    |
    | Config surface only for now — not consumed anywhere yet. Intended for
    | App\Audit\Performance\PageSpeedInsightsClient, which will let
    | PerformanceAnalyzer report real LCP/CLS/FID instead of "unknown"
    | when a key is configured. `enabled` defaults to false whenever no
    | key is set, so an empty PAGESPEED_API_KEY never accidentally turns
    | this on and starts throwing on missing-key API calls.
    |
    */
    'pagespeed' => [
        'api_key' => env('PAGESPEED_API_KEY'),
        'enabled' => env('PAGESPEED_API_KEY') !== null && env('PAGESPEED_API_KEY') !== '',
    ],

];