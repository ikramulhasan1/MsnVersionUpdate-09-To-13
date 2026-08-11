{{--
    PDF "Charts" section: a horizontal bar per category score, plus a
    stacked severity bar for the Critical/Warning/Notice breakdown.

    Built with plain HTML/CSS bar widths (percentage-based) rather than
    SVG or a JS charting library: dompdf's SVG/CSS support is limited
    and inconsistent across versions, while width-percentage bars
    render reliably in every dompdf version — the safe, well-established
    pattern for charts in a dompdf-generated PDF.

    The bar chart (Part 3 / Prompt 17.3) is laid out as a table with
    percentage column widths rather than fixed-pixel inline-block spans,
    so it can never overflow the page regardless of paper size or a
    long category name — the same overflow-safety approach used by the
    Scores/Recommendations/Summary tables.

    Every bar width is computed from $content (an
    App\Audit\Export\Pdf\DTO\PdfContentData) — nothing here is a fixed
    number; a bar's width is always literally the row's own score or
    count value.
--}}
<section class="pdf-section">
    <div class="pdf-section-title-bar">
        <h2 class="pdf-section-title">Category Score Chart</h2>
    </div>

    @if ($content->scoreRows->isEmpty())
        <p class="pdf-empty">No score data is available to chart yet.</p>
    @else
        <table class="pdf-chart-table">
            <colgroup>
                <col style="width: 24%;">
                <col style="width: 56%;">
                <col style="width: 20%;">
            </colgroup>
            <tbody>
                @foreach ($content->scoreRows as $row)
                    <tr>
                        <td class="pdf-chart-label">{{ $row->category }}</td>
                        <td class="pdf-chart-track-cell">
                            <div class="pdf-bar-track">
                                <div class="pdf-bar-fill" style="width: {{ $row->score ?? 0 }}%;"></div>
                            </div>
                        </td>
                        <td class="pdf-chart-value">{{ $row->score !== null ? $row->score . '/100' : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="pdf-section-title-bar pdf-section-title-spaced">
        <h2 class="pdf-section-title">Issue Severity Breakdown</h2>
    </div>

    @if ($content->severityCounts === null)
        <p class="pdf-empty">No issue severity data is available yet.</p>
    @else
        @php
            $severityTotal = max(array_sum($content->severityCounts), 1);
        @endphp
        <div class="pdf-severity-bar">
            <div class="pdf-severity-segment pdf-severity-critical" style="width: {{ $content->severityCounts['critical'] / $severityTotal * 100 }}%;"></div>
            <div class="pdf-severity-segment pdf-severity-warning" style="width: {{ $content->severityCounts['warning'] / $severityTotal * 100 }}%;"></div>
            <div class="pdf-severity-segment pdf-severity-notice" style="width: {{ $content->severityCounts['notice'] / $severityTotal * 100 }}%;"></div>
        </div>
        <table class="pdf-table pdf-severity-legend">
            <colgroup>
                <col style="width: 20px;">
                <col style="width: 30%;">
                <col style="width: 70%;">
            </colgroup>
            <tbody>
                <tr>
                    <td class="pdf-legend-swatch pdf-severity-critical"></td>
                    <td>Critical</td>
                    <td>{{ $content->severityCounts['critical'] }}</td>
                </tr>
                <tr>
                    <td class="pdf-legend-swatch pdf-severity-warning"></td>
                    <td>Warning</td>
                    <td>{{ $content->severityCounts['warning'] }}</td>
                </tr>
                <tr>
                    <td class="pdf-legend-swatch pdf-severity-notice"></td>
                    <td>Notice</td>
                    <td>{{ $content->severityCounts['notice'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</section>
