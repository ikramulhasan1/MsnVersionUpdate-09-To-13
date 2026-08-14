{{--
    PDF "Recommendations" section: one row per prioritized issue,
    reusing App\Audit\Export\DTO\RecommendationRow — the same shape the
    Excel export's Recommendations worksheet uses (see
    $content->recommendationRows, built by AnalysisResultsToPdfContentData
    from RecommendationResultToRows::recommendations()).

    Expects $content — an App\Audit\Export\Pdf\DTO\PdfContentData.
    Empty whenever the AI Recommendation Engine hasn't run for this
    audit yet, in which case a note is shown instead of an empty table.

    This is typically the longest table in the report, so it's the one
    most likely to span multiple pages — the <colgroup> (Part 3 /
    Prompt 17.3) keeps its six columns within the page width no matter
    how long an issue or recommendation string is, and the global
    `thead { display: table-header-group }` rule (layout.blade.php)
    repeats the column headers on every page it continues onto.

    $row->pageUrl (present on RecommendationRow since the AI
    Recommendation Engine started carrying page-level attribution) is
    now rendered as a compact "Page" column — shown only when at least
    one row actually has one, so audits from before that attribution
    existed don't render an empty column.

    The "04 / Recommendations" eyebrow (Part 6 — section numbering)
    continues the same numbered sequence scores.blade.php/charts.blade.php
    use (see scores.blade.php's docblock for why PDF numbering starts at
    02). Recommendations has no distinct top-level numbered section on
    the web dashboard — there, recommendations surface per-category
    inside "05 / Detailed Results" instead — so "04" here is simply the
    next number in the PDF's own linear reading order, not a reused web
    label.

    Part 9 (table refinement): the Severity column now renders as a
    colored .pdf-pill (see report.blade.php's pushed styles for the
    shared pill shape) instead of plain text — critical/warning/notice
    use the exact same red/amber/muted colors the Charts section's
    severity legend already established, so a color means the same
    thing in both places. Only Severity is pill-ified here, not the
    leading "#" priority column: that's a plain ordinal rank (1, 2, 3…),
    not a categorical status, so a colored badge would add noise rather
    than help scanning.
--}}
<section class="pdf-section">
    <div class="pdf-section-heading">
        <span class="pdf-eyebrow">04 / Recommendations</span>
        <div class="pdf-section-title-bar">
            <h2 class="pdf-section-title">Recommendations</h2>
        </div>
    </div>

    @if ($content->recommendationRows->isEmpty())
        <p class="pdf-empty">No recommendations are available for this audit yet.</p>
    @else
        @php
            $hasPageUrls = $content->recommendationRows->contains(fn($row) => !empty($row->pageUrl));
        @endphp
        <table class="pdf-table">
            <colgroup>
                <col style="width: 5%;">
                <col style="width: 12%;">
                <col style="width: 20%;">
                <col style="width: 10%;">
                <col style="{{ $hasPageUrls ? 'width: 33%;' : 'width: 53%;' }}">
                @if ($hasPageUrls)
                    <col style="width: 20%;">
                @endif
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Issue</th>
                    <th>Severity</th>
                    <th>Recommendation</th>
                    @if ($hasPageUrls)
                        <th>Page</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($content->recommendationRows as $row)
                    @php
                        $severityColor = match (strtolower($row->severity)) {
                            'critical' => '#b23b32',
                            'warning' => '#b5791f',
                            default => '#5b6270',
                        };
                    @endphp
                    <tr>
                        <td>{{ $row->priority }}</td>
                        <td>{{ $row->category }}</td>
                        <td>{{ $row->issue }}</td>
                        <td><span class="pdf-pill"
                                style="background-color: {{ $severityColor }};">{{ ucfirst($row->severity) }}</span>
                        </td>
                        <td>{{ $row->recommendation ?? 'N/A' }}</td>
                        @if ($hasPageUrls)
                            <td class="pdf-table-small">{{ $row->pageUrl ?? '—' }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
