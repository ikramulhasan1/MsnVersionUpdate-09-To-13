{{--
    PDF "Scores" section: one card per analyzed category, reusing the
    same App\Audit\Export\DTO\ScoreRow shape the Excel export's Scores
    worksheet already uses (see $content->scoreRows, built by
    AnalysisResultsToPdfContentData from AnalysisResultsToRows::scores()).

    Expects $content — an App\Audit\Export\Pdf\DTO\PdfContentData.
    Renders nothing but a "not available yet" note when no category has
    been analyzed, rather than an empty grid.

    Part 7 (score-card grid) replaces the old 4-column data table with a
    2-column table-based card grid — a <table> is still the underlying
    structure (dompdf has no flexbox/grid to lay real cards out with),
    but each cell is styled to read as a self-contained card: category
    name, a large score number, the letter grade, and a thin colored
    bottom border whose color reflects pass/warning/fail (green/amber/
    red), the same at-a-glance signal
    resources/views/audit/partials/dashboard.blade.php's own
    .summary-card gives on the web dashboard. 2 columns rather than 4:
    category names here ("Business Opportunity", "Accessibility") are
    long enough that 4 narrow columns on an A4 page would force
    cramped wrapping — 2 columns keeps every card comfortably readable
    while still reading as a grid rather than a list. Cards are grouped
    two-per-row via $content->scoreRows->chunk(2); a final odd row's
    single card spans both columns (colspan="2") rather than leaving a
    visibly empty cell beside it.

    The per-card accent color is derived from the row's own letter
    grade — A/B → green (pass), C/D → amber (warning), F → red (fail),
    no grade → neutral gray — the same three-tier mapping
    partials/header.blade.php's cover-page score badge already uses, so
    a color means the same thing everywhere in this PDF.
    page-break-inside: avoid on each card (not just the row) keeps a
    single card from ever being split across a page boundary, even
    though the underlying grid is still an ordinary HTML table.
--}}
<section class="pdf-section">
    <div class="pdf-section-heading">
        <span class="pdf-eyebrow">02 / Category Breakdown</span>
        <div class="pdf-section-title-bar">
            <h2 class="pdf-section-title">Category Scores</h2>
        </div>
    </div>

    @if ($content->scoreRows->isEmpty())
        <p class="pdf-empty">No category scores are available for this audit yet.</p>
    @else
        <table class="pdf-score-grid" role="presentation">
            <colgroup>
                <col style="width: 50%;">
                <col style="width: 50%;">
            </colgroup>
            <tbody>
                @foreach ($content->scoreRows->chunk(2) as $cardRow)
                    <tr>
                        @foreach ($cardRow as $row)
                            @php
                                $cardColor = match (true) {
                                    $row->grade === null => '#9ea3ad',
                                    in_array($row->grade, ['A', 'B'], true) => '#2f8f5e',
                                    in_array($row->grade, ['C', 'D'], true) => '#b5791f',
                                    default => '#b23b32',
                                };
                            @endphp
                            <td class="pdf-score-card-cell" @if ($cardRow->count() === 1) colspan="2" @endif>
                                <div class="pdf-score-card" style="border-bottom-color: {{ $cardColor }};">
                                    <div class="pdf-score-card-category">{{ $row->category }}</div>
                                    <div class="pdf-score-card-number" style="color: {{ $cardColor }};">
                                        {{ $row->score !== null ? $row->score : 'N/A' }}@if ($row->score !== null)
                                            <span class="pdf-score-card-max">/100</span>
                                        @endif
                                    </div>
                                    <div class="pdf-score-card-meta">
                                        <span class="pdf-score-card-grade"
                                            style="color: {{ $cardColor }};">{{ $row->grade ?? '—' }}</span>
                                        <span class="pdf-score-card-date">{{ $row->analyzedAt }}</span>
                                    </div>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
