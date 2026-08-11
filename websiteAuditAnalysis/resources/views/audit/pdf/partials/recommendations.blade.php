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
    Prompt 17.3) keeps its five columns within the page width no matter
    how long an issue or recommendation string is, and the global
    `thead { display: table-header-group }` rule (layout.blade.php)
    repeats the column headers on every page it continues onto.
--}}
<section class="pdf-section">
    <div class="pdf-section-title-bar">
        <h2 class="pdf-section-title">Recommendations</h2>
    </div>

    @if ($content->recommendationRows->isEmpty())
        <p class="pdf-empty">No recommendations are available for this audit yet.</p>
    @else
        <table class="pdf-table">
            <colgroup>
                <col style="width: 6%;">
                <col style="width: 14%;">
                <col style="width: 24%;">
                <col style="width: 12%;">
                <col style="width: 44%;">
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Issue</th>
                    <th>Severity</th>
                    <th>Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content->recommendationRows as $row)
                    <tr>
                        <td>{{ $row->priority }}</td>
                        <td>{{ $row->category }}</td>
                        <td>{{ $row->issue }}</td>
                        <td>{{ ucfirst($row->severity) }}</td>
                        <td>{{ $row->recommendation ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
