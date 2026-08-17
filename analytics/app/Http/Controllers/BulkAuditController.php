<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\Enums\AuditMode;
use App\Audit\Enums\BulkAuditBatchStatus;
use App\Audit\Export\BulkAuditBatchExport;
use App\Audit\Export\BulkAuditExportRowMapper;
use App\Audit\Services\BulkAuditBatchService;
use App\Http\Requests\StoreBulkAuditRequest;
use App\Models\BulkAuditBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase K3 (Bulk Audit) — two of this module's three submission entry
 * points (create()/store() below; the third is
 * DiscoveryController::bulkAudit()'s own existing "Bulk Audit
 * Selected" checkbox flow, extended in this same phase to call the
 * exact same App\Audit\Services\BulkAuditBatchService this controller
 * does).
 *
 * Phase K5 fills in show() (the real results dashboard — a live-
 * polling progress bar while the batch is still processing, then a
 * per-audit score table once it's done), progress() (the JSON
 * endpoint that polling reads from), and export() (Excel/CSV/JSON of
 * the same rows the table itself shows) — all three replacing what
 * was a deliberately minimal K3 placeholder.
 */
final class BulkAuditController extends Controller
{
    public function __construct(
        private readonly BulkAuditBatchService $bulkAuditBatchService,
    ) {
    }

    public function create(): View
    {
        return view('audit.bulk.create');
    }

    /**
     * Accepts EITHER a pasted URL-per-line textarea OR a CSV upload
     * (see StoreBulkAuditRequest's own docblock for why exactly one is
     * required, not both) — both end up as the same flat array of
     * strings before reaching BulkAuditBatchService::createBatch(),
     * which doesn't need to know or care which of the two a given URL
     * originally came from.
     */
    public function store(StoreBulkAuditRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $urls = $request->hasFile('csv')
            ? $this->parseCsv($request->file('csv')->getRealPath())
            : $this->parseTextarea($validated['urls'] ?? '');

        if ($urls === []) {
            return redirect()
                ->route('bulk-audits.create')
                ->withInput()
                ->with('status', 'No valid URLs were found in what you submitted.');
        }

        $mode = AuditMode::from($validated['mode']);

        $batch = $this->bulkAuditBatchService->createBatch(
            urls: $urls,
            mode: $mode,
            name: $validated['name'] ?? null,
        );

        return redirect()
            ->route('bulk-audits.show', $batch)
            ->with('status', sprintf(
                '%d website(s) queued for %s.',
                $batch->total_count,
                $mode->label(),
            ));
    }

    /**
     * $rows mirrors what BulkAuditExportRowMapper builds for the
     * Excel/CSV/JSON export (Phase K5) — the SAME shape backs the
     * on-screen table and every downloadable format, so a person never
     * sees a score on the page that the export doesn't also carry, or
     * vice versa. Recomputed on every request rather than cached
     * separately: while the batch is still processing this legitimately
     * needs to reflect whichever audits have finished BY NOW, and once
     * finished, reading AnalysisResults straight from
     * AuditCacheServiceInterface (already itself a cache) is cheap
     * enough not to need a second layer of caching on top.
     */
    public function show(BulkAuditBatch $bulkAuditBatch, BulkAuditExportRowMapper $rowMapper): View
    {
        $bulkAuditBatch->load('audits');

        return view('audit.bulk.show', [
            'batch' => $bulkAuditBatch,
            'rows' => $rowMapper->map($bulkAuditBatch),
        ]);
    }

    /**
     * Polled by public/js/bulk-audit-progress.js (Phase K5) roughly
     * once every few seconds while a batch is still processing —
     * mirrors AuditController::progress()'s own polling shape for a
     * single audit, rolled up to a whole batch's own
     * completed_count/failed_count/total_count instead of one audit's
     * percent/label. A batch has no separate progress-cache entry of
     * its own the way a single audit does (AuditCacheServiceInterface::
     * getProgress()) — its own three counters, updated as each child
     * audit finishes (see AssembleAnalysisResultsJob's own
     * updateBulkAuditBatchIfAny(), and BulkFetchJob::failed()'s own
     * catastrophic-failure path), are already a complete, cheap-to-read
     * picture of where the batch stands.
     */
    public function progress(BulkAuditBatch $bulkAuditBatch): JsonResponse
    {
        return response()->json([
            'status' => $bulkAuditBatch->status->value,
            'completed' => $bulkAuditBatch->completed_count,
            'failed' => $bulkAuditBatch->failed_count,
            'total' => $bulkAuditBatch->total_count,
            'percent' => $bulkAuditBatch->progressPercent(),
            'finished' => $bulkAuditBatch->status === BulkAuditBatchStatus::COMPLETED,
        ]);
    }

    /**
     * Backs the results table's own Export button (Phase K5) —
     * ?format=excel|csv|json, defaulting to excel. Reads from the exact
     * same BulkAuditExportRowMapper show() itself uses, so an export
     * always matches whatever's currently on screen (the same
     * reasoning DiscoveryController::export()'s own docblock already
     * documents for a completely different module's export).
     *
     * Unlike DiscoveryController::export()'s own PDF-related incident
     * history, this method only ever needs Excel — there is no PDF
     * branch here to isolate, so Excel stays a plain constructor-
     * injected dependency rather than needing the method-injection
     * workaround that fixed a real production issue there.
     */
    public function export(BulkAuditBatch $bulkAuditBatch, Request $request, Excel $excel, BulkAuditExportRowMapper $rowMapper): Response|JsonResponse
    {
        $format = strtolower((string) $request->query('format', 'excel'));

        $bulkAuditBatch->load('audits');
        $rows = $rowMapper->map($bulkAuditBatch);

        return match ($format) {
            'json' => response()->json(['audits' => $rows]),
            'csv' => $excel->download(
                new BulkAuditBatchExport($rows),
                sprintf('bulk-audit-%s.csv', $bulkAuditBatch->uuid),
            ),
            default => $excel->download(
                new BulkAuditBatchExport($rows),
                sprintf('bulk-audit-%s.xlsx', $bulkAuditBatch->uuid),
            ),
        };
    }

    /**
     * One URL per non-empty line — no delimiter sniffing, no comma-
     * splitting within a line, since a URL itself can legitimately
     * contain commas in its query string. Blank lines (including
     * trailing whitespace-only ones from how a textarea's own value
     * naturally ends) are silently dropped rather than becoming an
     * empty-string "URL" BulkAuditBatchService would otherwise have to
     * filter out itself.
     *
     * @return array<int, string>
     */
    private function parseTextarea(string $raw): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $raw) ?: [])
            ->map(static fn (string $line): string => trim($line))
            ->filter(static fn (string $line): bool => $line !== '')
            ->values()
            ->all();
    }

    /**
     * Reads every row's FIRST column as a URL — deliberately not
     * "the column literally named url", since a person's own CSV
     * export (from a spreadsheet, another tool, etc.) may not have a
     * header row at all, or may name that column something else
     * entirely. A row whose first column doesn't look like it starts
     * with http:// or https:// is skipped rather than queued as a
     * doomed-to-fail audit — this is what lets a CSV WITH a header row
     * (e.g. "Website,Notes") work without any special-casing: the
     * header row's own first cell fails this same check and is simply
     * skipped like any other non-URL row would be.
     *
     * @return array<int, string>
     */
    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [];
        }

        $urls = [];

        while (($row = fgetcsv($handle)) !== false) {
            $candidate = trim((string) ($row[0] ?? ''));

            if (preg_match('#^https?://#i', $candidate) === 1) {
                $urls[] = $candidate;
            }
        }

        fclose($handle);

        return $urls;
    }
}