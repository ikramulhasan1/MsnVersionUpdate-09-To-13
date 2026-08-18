{{--
    Reusable PDF cover/header section: company logo, report title,
    website URL, generated date, and a circular overall-score badge —
    everything a reader needs at a glance on page 1, before the detailed
    Scores/Charts/Recommendations/Summary sections that follow.

    A plain include rather than a Blade component, matching this
    project's existing Blade convention (see audit/partials/*.blade.php)
    of @@include-based partials over anonymous components.

    Expects:
      $header    an App\Audit\Export\Pdf\DTO\PdfHeaderData — logo, company
                 name, website URL, and formatted audit date.
      $content   an App\Audit\Export\Pdf\DTO\PdfContentData — only
                 ->scoreRows is read here, to derive the overall score
                 badge below.

    The overall score/grade are deliberately derived here (a plain
    average of every non-null ScoreRow, using the same A/B/C/D/F
    threshold convention every analyzer and AIRecommendationEngine
    already use) rather than added as a new field on PdfHeaderData or
    PdfContentData: it's simple, presentation-only arithmetic over data
    the view already has, in the same spirit as
    dashboard-components.blade.php's own inline pass/warning/fail tallies
    for its chart rendering — adding a dedicated mapper/DTO field for one
    number used only here would be more machinery than the calculation
    warrants. Renders a muted "not available yet" line instead of the
    badge when no category has scored, the same "note, don't fabricate"
    rule every other empty-state in this PDF already follows.

    Built as tables/divs with fixed widths and text-align/line-height
    centering rather than flexbox/grid: dompdf supports neither. See the
    "Cover / Header" comment in report.blade.php's pushed styles for the
    fuller rationale on the circular badge and font choices.
--}}
@php
    $overallScores = $content->scoreRows
        ->pluck('score')
        ->filter(static fn(?int $score): bool => $score !== null)
        ->values();

    $overallScore = $overallScores->isEmpty() ? null : (int) round($overallScores->avg());

    $overallGrade = match (true) {
        $overallScore === null => null,
        $overallScore >= 90 => 'A',
        $overallScore >= 75 => 'B',
        $overallScore >= 60 => 'C',
        $overallScore >= 40 => 'D',
        default => 'F',
    };

    $scoreBadgeColor = match (true) {
        $overallGrade === null => '#9c7a3c',
        in_array($overallGrade, ['A', 'B'], true) => '#2f8f5e',
        in_array($overallGrade, ['C', 'D'], true) => '#b5791f',
        default => '#b23b32',
    };
@endphp
<div class="pdf-cover">
    <table role="presentation" class="pdf-cover-logo-table">
        <tr>
            <td class="pdf-cover-logo-cell">
                @if ($header->logoDataUri)
                    <img src="{{ $header->logoDataUri }}" alt="{{ $header->companyName }}" class="pdf-cover-logo-img">
                @else
                    <span class="pdf-cover-logo-text">{{ $header->companyName }}</span>
                @endif
            </td>
        </tr>
    </table>

    <h1 class="pdf-cover-title">Website Audit Report</h1>

    <p class="pdf-cover-url">{{ $header->websiteUrl }}</p>
    <p class="pdf-cover-date">Generated {{ $header->auditDate }}</p>

    @if ($overallScore !== null)
        <table role="presentation" class="pdf-score-badge-table">
            <tr>
                <td class="pdf-score-badge-cell">
                    <div class="pdf-score-badge" style="background-color: {{ $scoreBadgeColor }};">
                        <div class="pdf-score-badge-number">{{ $overallScore }}</div>
                    </div>
                </td>
            </tr>
        </table>
        <p class="pdf-score-badge-caption">
            Overall Score: {{ $overallScore }} / 100@if ($overallGrade)
                &nbsp;&mdash;&nbsp;Grade {{ $overallGrade }}
            @endif
        </p>
    @else
        <p class="pdf-score-badge-empty">Overall score not available yet.</p>
    @endif

    <div class="pdf-cover-divider"></div>
</div>
