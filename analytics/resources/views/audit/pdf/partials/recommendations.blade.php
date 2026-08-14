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
--}}
<section class="pdf-section">
    <div class="pdf-section-title-bar">
        <h2 class="pdf-section-title">Recommendations</h2>
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
                    <tr>
                        <td>{{ $row->priority }}</td>
                        <td>{{ $row->category }}</td>
                        <td>{{ $row->issue }}</td>
                        <td>{{ ucfirst($row->severity) }}</td>
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
