{{--
    PDF "Summary" section: a label/value overview, reusing
    App\Audit\Export\DTO\SummaryRow — the same shape the Excel export's
    Summary worksheet uses (see $content->summaryRows, built by
    AnalysisResultsToPdfContentData from SummaryResultsToRows::summary()).

    Expects $content — an App\Audit\Export\Pdf\DTO\PdfContentData.
    summaryRows always has at least the Audit URL row (see
    SummaryResultsToRows), so the empty-state note here only appears if
    that mapper's contract ever changes.

    The <colgroup> (Part 3 / Prompt 17.3) gives the label column a
    fixed share of the page width instead of the narrower fixed-pixel
    width used before, so a long narrative value in the second column
    still wraps within the page rather than overflowing it.

    The "05 / Summary" eyebrow (Part 6 — section numbering) continues
    the PDF's own linear numbering (see scores.blade.php's docblock for
    why it starts at 02) — the web dashboard has no separate top-level
    "Summary" section of its own to match against, so "05" here is
    simply the final number in the PDF's reading order.

    Part 9 (table refinement): a row whose label is a known Priority
    field ("Recommendation Priority", "Opportunity Priority" — see
    SummaryResultsToRows) or "Overall Grade" renders its value as a
    colored .pdf-pill (see report.blade.php's pushed styles for the
    shared pill shape) instead of plain text, so the handful of values
    in this label/value table that are actually a status — not a
    number, URL, or narrative sentence — stand out for quick scanning.
    Priority uses High → red / Medium → amber / Low → green (green,
    not muted, since a Low priority is a good outcome — unlike
    Recommendations' severity pills, which reuse the Charts section's
    critical/warning/notice colors instead); Overall Grade reuses the
    exact same A/B → green, C/D → amber, F → red mapping
    partials/header.blade.php's cover-page score badge and
    partials/scores.blade.php's score cards already use. Every other
    row's value is plain text, unchanged.
--}}
<section class="pdf-section">
    <div class="pdf-section-heading">
        <span class="pdf-eyebrow">05 / Summary</span>
        <div class="pdf-section-title-bar">
            <h2 class="pdf-section-title">Summary</h2>
        </div>
    </div>

    @if ($content->summaryRows->isEmpty())
        <p class="pdf-empty">No summary data is available for this audit yet.</p>
    @else
        <table class="pdf-table pdf-summary-table">
            <colgroup>
                <col style="width: 32%;">
                <col style="width: 68%;">
            </colgroup>
            <tbody>
                @foreach ($content->summaryRows as $row)
                    @php
                        $isPriorityRow = in_array(
                            $row->label,
                            ['Recommendation Priority', 'Opportunity Priority'],
                            true,
                        );

                        $pillColor = match (true) {
                            $isPriorityRow => match (strtolower($row->value)) {
                                'high' => '#b23b32',
                                'medium' => '#b5791f',
                                'low' => '#2f8f5e',
                                default => null,
                            },
                            $row->label === 'Overall Grade' => match (true) {
                                in_array($row->value, ['A', 'B'], true) => '#2f8f5e',
                                in_array($row->value, ['C', 'D'], true) => '#b5791f',
                                $row->value === 'F' => '#b23b32',
                                default => null,
                            },
                            default => null,
                        };
                    @endphp
                    <tr>
                        <th>{{ $row->label }}</th>
                        <td>
                            @if ($pillColor !== null)
                                <span class="pdf-pill"
                                    style="background-color: {{ $pillColor }};">{{ $row->value }}</span>
                            @else
                                {{ $row->value }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
