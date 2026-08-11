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
--}}
<section class="pdf-section">
    <div class="pdf-section-title-bar">
        <h2 class="pdf-section-title">Summary</h2>
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
                    <tr>
                        <th>{{ $row->label }}</th>
                        <td>{{ $row->value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
