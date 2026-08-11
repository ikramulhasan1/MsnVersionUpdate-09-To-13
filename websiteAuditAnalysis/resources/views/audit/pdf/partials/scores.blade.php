{{--
    PDF "Scores" section: one row per analyzed category, reusing the
    same App\Audit\Export\DTO\ScoreRow shape the Excel export's Scores
    worksheet already uses (see $content->scoreRows, built by
    AnalysisResultsToPdfContentData from AnalysisResultsToRows::scores()).

    Expects $content — an App\Audit\Export\Pdf\DTO\PdfContentData.
    Renders nothing but a "not available yet" note when no category has
    been analyzed, rather than an empty table.

    The <colgroup> (Part 3 / Prompt 17.3) fixes each column's width as a
    percentage of the page so the table can never overflow — combined
    with the global `table-layout: fixed` + word-wrap rules in
    layout.blade.php, this is what keeps a long timestamp or category
    name from pushing the table past the page edge.
--}}
<section class="pdf-section">
    <div class="pdf-section-title-bar">
        <h2 class="pdf-section-title">Category Scores</h2>
    </div>

    @if ($content->scoreRows->isEmpty())
        <p class="pdf-empty">No category scores are available for this audit yet.</p>
    @else
        <table class="pdf-table">
            <colgroup>
                <col style="width: 30%;">
                <col style="width: 18%;">
                <col style="width: 18%;">
                <col style="width: 34%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Score</th>
                    <th>Grade</th>
                    <th>Analyzed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content->scoreRows as $row)
                    <tr>
                        <td>{{ $row->category }}</td>
                        <td>{{ $row->score !== null ? $row->score . ' / 100' : 'N/A' }}</td>
                        <td>{{ $row->grade ?? 'N/A' }}</td>
                        <td>{{ $row->analyzedAt }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
