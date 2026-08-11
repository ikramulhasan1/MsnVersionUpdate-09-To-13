# Prompt 20.2 — Performance, Coverage & Bug Fixes

## ⚠️ Execution disclaimer (unchanged from Prompt 20.1)

This sandbox has no PHP interpreter and no Packagist access, so `composer install`
and `php artisan test` could not actually be run here. Every test below was written
against the real source (constructor signatures, DTO shapes, and existing test
conventions were all read from the actual files, not assumed) and manually checked
for brace/paren balance, but **no test in this project — old or new — has been
executed in this session.** Run `composer install && php artisan test` locally or
in CI to get real pass/fail numbers.

---

## Performance Tests (new: `tests/Performance/`, 4 files)

| File | Workflow under test | What it guards against |
|---|---|---|
| `AIRecommendationEnginePerformanceTest.php` | The 964-line `AIRecommendationEngine::analyze()` — runs synchronously inside `AssembleAnalysisResultsJob`, so its cost is user-facing wait time | Time budget on a healthy page, time+memory budget on a large broken page (500 unlabeled images, 200 paragraphs, zero security headers — worst case for issue volume), and a 20-run repeated-execution check for accumulating/shared state |
| `WebsiteCrawlerServicePerformanceTest.php` | `WebsiteCrawlerService::crawl()` BFS over a simulated site (fakes replace real HTTP) | Time+memory on a 300-page densely-linked mesh; confirms external-link checking is memoized per unique URL (not per referring page); confirms `maxPages` bounds actual fetch work on a 5,000-page graph, not just the returned count |
| `HtmlParserPerformanceTest.php` | `HtmlParser::parse()` — DOMDocument/DOMXPath, runs on every fetched page | Time+memory on a 2,000-image/2,000-link/500-paragraph page; a 200-vs-2,000-image scaling check to catch an accidental quadratic pass; a deep-nesting (500 levels) case |
| `AuditCacheServicePerformanceTest.php` | `AuditCacheService` fragment read/write — shared cache store under concurrent queue workers | Write/read time across 500 simultaneously in-flight audits' fragments; an isolation check that forgetting one audit's fragments doesn't affect a neighboring audit's cache keys |

All budgets are intentionally generous (order-of-magnitude tripwires, not tight
SLAs) since absolute timings vary by hardware/CI load — the goal is to catch a
regression like an accidental O(n²) pass or unbounded growth, not to benchmark
precisely.

---

## Coverage Findings

Comparing `app/` (132 PHP files) to `tests/` (27 files before this pass): the
**analyzer/DTO core is well covered** (every analyzer, the AI recommendation
engine, the cache service, validation, fetching, crawling, and the two feature
flows all have tests). The gaps are concentrated in the **orchestration and
export layers**, which never got dedicated tests across either Prompt 20.1 or
20.2's scope:

- **`app/Audit/Jobs/*` (5 files, 0 direct unit tests).** `AnalyzeChunkJob`,
  `AssembleAnalysisResultsJob`, `FetchAndCrawlJob`, and `AuditJob`/
  `HasAuditUniqueness` are only touched indirectly by
  `tests/Integration/Providers/ServiceContainerBindingsTest.php` (which checks
  they're *resolvable*, not that `handle()` behaves correctly — e.g. the
  partial-fragment → `AuditStatus::FAILED` logic in
  `AssembleAnalysisResultsJob::handle()` has no test exercising it).
- **`app/Audit/Export/*` (18 files, 0 tests).** PDF export, Excel/Sheets export,
  and the API DTO mapper (`AnalysisResultsToApiData`) have no coverage at all.
  This was explicitly marked out-of-scope in the Prompt 20.1 summary ("PDF/Excel
  export tests — can add in a later step") and is still open.
- **`app/Audit/Repositories/AuditRepository.php` and
  `app/Audit/Services/AuditService.php`** have no dedicated unit tests — they're
  only exercised indirectly through `AuditSubmissionFlowTest` (a Feature test),
  so e.g. `AuditRepository::findLatestPendingByUrl()`'s status-filtering logic
  isn't isolated anywhere.
- **`app/Audit/Crawler/LinkChecker.php`** (the real Guzzle-backed
  implementation) is never tested directly — only `FakeLinkChecker` is used
  across the suite. Its HEAD→GET fallback on 405/501 and the `Range` header
  logic are untested.
- **Dead code found, not a bug:** `app/Http/Controllers/Api/AuditController.php`
  (unversioned) is not wired into `routes/api.php` at all — only
  `Api\V1\AuditController` is routed. The unversioned controller appears to be
  a leftover from before API versioning was introduced. Left untouched per
  "no unnecessary refactoring," but flagged since it explains why it has no
  test coverage and arguably shouldn't need any.

---

## Bugs Fixed

**1. `tests/Integration/` was never registered in `phpunit.xml`.** The
container-binding test written in the prior session
(`ServiceContainerBindingsTest.php`) lived in `tests/Integration/Providers/`,
but `phpunit.xml` only declared `Unit` and `Feature` test suites pointing at
`tests/Unit` and `tests/Feature`. Under both `vendor/bin/phpunit` and
`php artisan test`, that test would **silently never run** — it existed but was
invisible to the suite, so the exact regression it was written to catch
(the missing `AuditServiceProvider` / missing `api:` routing registration,
both fixed in Prompt 20.1) could recur without anything failing. Fixed by
adding an `Integration` test suite to `phpunit.xml`. This is a test-harness
configuration bug, not application logic, so it's within the "fix bugs
discovered during testing" mandate without touching production code.

**2. `tests/Performance/` registered for the same reason**, alongside the fix
above — a new test directory with no corresponding `<testsuite>` entry is the
same class of invisible-test bug, just pre-empted here rather than discovered
after the fact.

No further application-logic bugs were found in this pass. `AnalyzeChunkJob`,
`AssembleAnalysisResultsJob`, `AuditService`, `AuditRepository`, `LinkChecker`,
and `StoreAuditRequest` were all read closely (as the least-covered files) for
this report's coverage findings, and no incorrect behavior was found in them —
only the absence of tests for them, which is a coverage gap, not a bug, and
per instructions is reported rather than "fixed" by inventing tests beyond the
requested Performance scope.

---

## Remaining Issues

1. **Cannot confirm pass/fail** — no PHP runtime in this environment. Run
   `composer install && php artisan test` (or `vendor/bin/phpunit`) to get
   real results; the `Integration` and `Performance` suites will now actually
   execute as part of that run.
2. **Export layer (PDF/Excel/API mapper) has zero test coverage** — largest
   remaining gap, was out-of-scope for both 20.1 and 20.2's stated scope
   (Performance Tests / Coverage Review / Bug Fixes only).
3. **Jobs pipeline behavior** (not just container resolution) is untested —
   `AssembleAnalysisResultsJob`'s partial-fragment failure path in particular.
4. **`AuditRepository` / `AuditService` / real `LinkChecker`** have no unit
   tests of their own, only indirect Feature-test coverage.
5. Per the stated scope for this prompt ("Implement only: Performance Tests,
   Test Coverage Review, Bug Fixes... do not perform unnecessary refactoring"),
   none of the above gaps were filled with new unit tests in this pass — they're
   reported as findings for a future prompt to address, consistent with how
   Prompt 20.1 scoped out PDF/Excel export tests.
