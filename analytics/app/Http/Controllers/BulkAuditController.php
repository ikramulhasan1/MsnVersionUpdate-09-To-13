<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\Enums\AuditMode;
use App\Audit\Services\BulkAuditBatchService;
use App\Http\Requests\StoreBulkAuditRequest;
use App\Models\BulkAuditBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Phase K3 (Bulk Audit) — two of this module's three submission entry
 * points (create()/store() below; the third is
 * DiscoveryController::bulkAudit()'s own existing "Bulk Audit
 * Selected" checkbox flow, extended in this same phase to call the
 * exact same App\Audit\Services\BulkAuditBatchService this controller
 * does). show() is a deliberately minimal placeholder for now — Phase
 * K5 replaces its view with the real results dashboard (per-audit
 * scores, a live-polling progress bar, export); this phase only needs
 * somewhere real for store() to redirect to so a bulk submission has
 * an immediate, working confirmation page rather than a 404.
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

    public function show(BulkAuditBatch $bulkAuditBatch): View
    {
        return view('audit.bulk.show', [
            'batch' => $bulkAuditBatch,
        ]);
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