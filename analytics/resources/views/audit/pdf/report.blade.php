{{--
    Root template for the audit PDF report.

    Part 1 (Prompt 17.1) scoped this to only the header section. Part 2
    (Prompt 17.2) added the main content — Scores, Charts, Recommendations
    and Summary — as four partials, each bound to $content (an
    App\Audit\Export\Pdf\DTO\PdfContentData). Part 3 (Prompt 17.3) is a
    styling-only pass: professional typography, clearer section
    separation, overflow-safe table/chart widths, and print-friendly
    page breaks. No partial's markup or data binding changed — every
    class below either already existed (Part 1/2) or is purely additive.
    The header include and the base layout's structure are untouched;
    only the shared page setup, typography and footer in layout.blade.php
    were finalized there.

    Part 4 (PDF redesign) is a color-only pass: every hex value below is
    swapped for the exact value public/css/app.css's :root design tokens
    use in light mode, so the PDF and the web dashboard read as the same
    brand — see the comment at the top of the @push('styles') block
    below for the full token list. No selector, layout rule, or class
    name changed.

    Part 5 (PDF cover redesign) replaces the old two-column .pdf-header
    table with a full, vertically-stacked cover section (see the
    "Cover / Header" comment block below and partials/header.blade.php
    itself) — the header partial now also receives $content so it can
    compute and display an overall score badge alongside the logo,
    title, URL and date.

    Part 6 (section numbering) adds a numbered .pdf-eyebrow label above
    each major section's title bar (Scores/Charts/Recommendations/
    Summary), matching the "NN / Label" convention
    dashboard*.blade.php's .report-eyebrow already uses on the web
    report, plus a faint brass-tinted background on .pdf-section-title-bar
    itself — see the "Section shell" comment below and each partial's
    own docblock for the exact numbering/labels used.

    Part 7 (score-card grid) replaces the "Category Scores" section's
    old 4-column data table with a 2-column table-based card grid — see
    the "Score Card Grid" comment below and partials/scores.blade.php
    itself for the full rationale (column count, per-card accent
    colors, page-break handling).

    Part 8 (chart bar polish) gives the Category Score Chart's bars a
    two-tone brass fill (in place of a real CSS gradient, which dompdf
    doesn't reliably support), a taller track/bar, and h6/.small-like
    label/value typography — see the "Charts" comment below and
    partials/charts.blade.php itself for the full rationale.

    Part 9 (Recommendations/Summary table refinement) darkens
    .pdf-table thead th to #7c5f2c (app.css's own --audit-primary-dark)
    for a heavier header tone on these text-dense tables, and adds
    .pdf-pill — a shared colored-badge style both partials use for
    their Severity/Priority/Grade values, so a text-heavy table is
    easier to scan at a glance. Row striping is unchanged (already
    --audit-surface-alt #f7f6f2 from Part 4). See the "Tables" comment
    below and each partial's own docblock for exactly which values get
    a pill and how their color is chosen.
--}}
@extends('audit.pdf.layout')

@section('title', 'Website Audit Report - ' . $header->websiteUrl)

@push('styles')
    /* ==========================================================
    Cover / Header (Part 5 — premium cover-page redesign) — a
    full, vertically-stacked cover section at the top of page 1:
    a larger logo, a big serif title, the audited URL and
    generated date, and a circular overall-score badge, closed
    off by a thin brass accent divider.

    Built entirely from tables/divs with fixed widths and
    text-align/line-height centering (no flexbox/grid — dompdf
    supports neither); the circular badge is a single fixed-size
    div with border-radius: 50%, which dompdf does render
    reliably for a simple filled shape like this, with the score
    number vertically centered via line-height equal to the
    div's own height (dompdf has no vertical-align equivalent
    for block content, so this is the standard dompdf-safe
    centering trick for a single line of text).

    'DejaVu Serif' is the title/badge font — the dompdf-safe
    fallback for app.css's Fraunces display font, which isn't
    registered with dompdf (see layout.blade.php's docblock for
    why an unregistered font name is never substituted directly).

    Colors are the same public/css/app.css hex values as the rest
    of this file's Part 4 color-only pass (see that pass's token
    list above); the badge's own background additionally reflects
    the overall letter grade (A/B → --audit-success #2f8f5e, C/D
    → --audit-warning #b5791f, F → --audit-danger #b23b32) so the
    very first thing a reader sees signals how the site did, not
    just a static number.
    ========================================================== */
    .pdf-cover {
    text-align: center;
    padding-bottom: 16px;
    margin-bottom: 26px;
    }

    .pdf-cover-logo-table {
    width: 100%;
    margin-bottom: 12px;
    }

    .pdf-cover-logo-cell {
    text-align: center;
    }

    .pdf-cover-logo-img {
    max-width: 200px;
    max-height: 90px;
    }

    .pdf-cover-logo-text {
    font-family: 'DejaVu Serif', serif;
    font-size: 24px;
    font-weight: bold;
    color: #9c7a3c;
    }

    .pdf-cover-contact {
    text-align: center;
    font-size: 10px;
    color: #5b6270;
    margin: 6px 0 14px;
    }

    .pdf-cover-title {
    font-family: 'DejaVu Serif', serif;
    font-size: 30px;
    font-weight: bold;
    color: #15181f;
    margin: 4px 0 12px;
    }

    .pdf-cover-url {
    font-size: 13px;
    color: #5b6270;
    margin: 0 0 3px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    }

    .pdf-cover-date {
    font-size: 11px;
    color: #5b6270;
    margin: 0 0 22px;
    }

    .pdf-score-badge-table {
    width: 100%;
    margin-bottom: 8px;
    }

    .pdf-score-badge-cell {
    text-align: center;
    }

    /* PRODUCTION INCIDENT — see resources/views/audit/pdf/partials/header.blade.php's
       own comment at this same badge's markup for the full story: this
       used to be a single <div style="width:104px;height:104px;border-radius:52px">
       with a nested <div style="line-height:104px"> for vertical
       centering, which dompdf rendered with the number's own baseline
       well below the circle's own clipped boundary. Replaced with a
       one-cell HTML table using vertical-align: middle, centered
       explicitly rather than left to rely on the table's own natural
       layout. */
    .pdf-score-badge-outer {
    width: 104px;
    height: 104px;
    border-radius: 52px;
    border-collapse: collapse;
    margin: 0 auto;
    }

    .pdf-score-badge-number {
    width: 104px;
    height: 104px;
    font-family: 'DejaVu Serif', serif;
    font-size: 36px;
    font-weight: bold;
    color: #ffffff;
    text-align: center;
    vertical-align: middle;
    }

    .pdf-score-badge-caption {
    font-size: 11px;
    color: #5b6270;
    margin: 0 0 18px;
    }

    .pdf-score-badge-empty {
    font-size: 10.5px;
    color: #5b6270;
    font-style: italic;
    margin: 0 0 18px;
    }

    .pdf-cover-divider {
    width: 100%;
    height: 2px;
    background-color: #9c7a3c;
    margin-top: 4px;
    }

    /* ==========================================================
    Section shell — shared by Scores, Charts, Recommendations
    and Summary. A left accent bar + title gives each section a
    clear, consistent visual start; page-break-inside: avoid on
    the title bar keeps a heading from ever landing alone at the
    bottom of a page.

    Part 6 (section numbering) adds .pdf-eyebrow — a small brass,
    monospaced, numbered label placed directly above each major
    section's title bar (e.g. "02 / Category Breakdown"), matching
    resources/views/audit/partials/dashboard*.blade.php's own
    .report-eyebrow convention on the web dashboard so a reader
    moving between the PDF and the web report sees the same
    section sequence in both. See each partial's own docblock for
    exactly which number/label it uses and why. .pdf-section-heading
    wraps an eyebrow together with the title bar immediately below
    it in one page-break-inside: avoid group, so the two can never
    be separated by a page break (an eyebrow orphaned alone at the
    bottom of a page, with its title bar pushed to the next one,
    would be worse than no eyebrow at all). Part 6 also adds a
    faint brass-tinted background to .pdf-section-title-bar itself,
    on top of its already-brass left border/title color (Part 4),
    so the accent reads clearly even before the eyebrow/title text
    is read.
    ========================================================== */
    .pdf-section {
    margin-bottom: 26px;
    }

    .pdf-section-heading {
    page-break-inside: avoid;
    page-break-after: avoid;
    }

    .pdf-eyebrow {
    display: block;
    font-family: 'DejaVu Sans Mono', monospace;
    font-size: 9px;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #9c7a3c;
    margin: 0 0 5px;
    }

    .pdf-section-title-bar {
    border-left: 3px solid #9c7a3c;
    background-color: #faf6ee;
    padding: 4px 0 4px 10px;
    margin: 0 0 10px;
    page-break-inside: avoid;
    page-break-after: avoid;
    }

    .pdf-section-title {
    font-size: 14px;
    font-weight: bold;
    color: #9c7a3c;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin: 0;
    }

    .pdf-section-title-spaced {
    margin-top: 22px;
    }

    .pdf-empty {
    font-size: 10.5px;
    color: #5b6270;
    font-style: italic;
    margin: 0 0 4px;
    }

    /* ==========================================================
    Score Card Grid (Part 7) — the "Category Scores" section's
    2-column table-based card grid. See scores.blade.php's own
    docblock for why 2 columns (not 4) and how each card's accent
    color is chosen. Every color here is a plain hex value, not a
    var(--audit-*) reference, for the same dompdf-compatibility
    reason as the rest of this file's Part 4 color pass; the three
    accent colors (green/amber/red) match --audit-success/-warning/
    -danger exactly, plus one neutral gray (#9ea3ad) for a category
    with no grade yet, which app.css has no token for since the web
    dashboard doesn't need a "no data" card color of its own.
    ========================================================== */
    .pdf-score-grid {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4px;
    }

    .pdf-score-card-cell {
    width: 50%;
    vertical-align: top;
    padding: 5px;
    }

    .pdf-score-card {
    border: 1px solid #e3e1d9;
    border-bottom-width: 4px;
    border-bottom-style: solid;
    border-radius: 6px;
    padding: 10px 12px;
    page-break-inside: avoid;
    }

    .pdf-score-card-category {
    font-size: 9.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #5b6270;
    margin-bottom: 6px;
    }

    .pdf-score-card-number {
    font-family: 'DejaVu Serif', serif;
    font-size: 28px;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 6px;
    }

    .pdf-score-card-max {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11px;
    font-weight: normal;
    color: #5b6270;
    }

    .pdf-score-card-meta {
    font-size: 9px;
    }

    .pdf-score-card-grade {
    display: inline-block;
    font-weight: bold;
    margin-right: 8px;
    }

    .pdf-score-card-date {
    color: #5b6270;
    }

    /* ==========================================================
    Tables — Recommendations, Summary. Fixed layout + column
    widths (set per-table via colgroup in each partial) is what
    guarantees a table never overflows the page width no matter
    how long a URL or issue description is; word-wrap (set
    globally in layout.blade.php) handles the rest.

    Part 9 (Recommendations/Summary table refinement): thead is
    now a darker brass (#7c5f2c, app.css's own --audit-primary-dark
    — the same darker brass tone .pdf-bar-fill-bottom already uses,
    Part 8) rather than the standard brass #9c7a3c every other
    accent in this PDF uses — a heavier, more authoritative tone
    for a text-dense table header specifically, distinct from
    lighter-touch accents like the section title bar. Row striping
    stays the same soft --audit-surface-alt tint (#f7f6f2) the
    Part 4 color pass already set. .pdf-pill (below) is the shared
    colored-badge style both partials use for their Severity/
    Priority/Grade values — see each partial's own docblock for
    exactly which values get a pill and how their color is chosen.
    ========================================================== */
    .pdf-table {
    font-size: 10px;
    margin-bottom: 4px;
    }

    .pdf-table th,
    .pdf-table td {
    border: 1px solid #e3e1d9;
    padding: 5px 7px;
    text-align: left;
    vertical-align: top;
    }

    .pdf-table thead th {
    background-color: #7c5f2c;
    color: #ffffff;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    }

    .pdf-table tbody tr:nth-child(even) {
    background-color: #f7f6f2;
    }

    .pdf-pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #ffffff;
    white-space: nowrap;
    }

    /* Compact "Page URL" columns (Recommendations) — smaller and
    muted so a long URL doesn't visually compete with the issue/
    recommendation text next to it. */
    .pdf-table-small {
    font-size: 8.5px;
    color: #5b6270;
    word-break: break-all;
    }

    .pdf-summary-table th {
    background-color: #eae8e0;
    font-weight: bold;
    white-space: nowrap;
    }

    /* ==========================================================
    Charts — a table-based bar chart (percentage column widths,
    not fixed pixel widths) so it resizes safely with the page
    instead of risking overflow, plus the severity legend.

    Part 8 (chart bar polish): .pdf-chart-label now carries
    h6-like weight (600, matching Bootstrap 5's own
    $headings-font-weight) so a category name reads as a small
    heading rather than plain body text; .pdf-chart-value is
    lighter and muted, matching Bootstrap's .small treatment as a
    secondary figure beside it — the same visual hierarchy
    app.css's own h6/.small pairing gives the web dashboard,
    scaled to this PDF's smaller base font size rather than
    copied at literal web pixel sizes. Track/bar height is also
    taller than before (14px/12px, up from 10px/8px) for better
    legibility. .pdf-bar-fill's two-tone brass fill (light band
    over dark band) is documented in charts.blade.php's own
    docblock.
    ========================================================== */
    .pdf-chart-table td {
    border: none;
    padding: 3px 6px;
    vertical-align: middle;
    }

    .pdf-chart-table tbody tr:nth-child(even) {
    background-color: transparent;
    }

    .pdf-chart-label {
    width: 24%;
    font-size: 10.5px;
    font-weight: 600;
    color: #15181f;
    }

    .pdf-chart-track-cell {
    width: 56%;
    }

    .pdf-chart-value {
    width: 20%;
    font-size: 9.5px;
    font-weight: normal;
    color: #5b6270;
    text-align: right;
    }

    .pdf-bar-track {
    width: 100%;
    height: 14px;
    background-color: #eae8e0;
    border: 1px solid #e3e1d9;
    border-radius: 3px;
    }

    .pdf-bar-fill {
    height: 12px;
    margin-top: 1px;
    border-radius: 2px;
    overflow: hidden;
    }

    .pdf-bar-fill-top {
    height: 6px;
    background-color: #c9a15f;
    }

    .pdf-bar-fill-bottom {
    height: 6px;
    background-color: #7c5f2c;
    }

    .pdf-severity-bar {
    width: 100%;
    height: 14px;
    margin-bottom: 10px;
    border: 1px solid #e3e1d9;
    }

    .pdf-severity-segment {
    display: inline-block;
    height: 14px;
    }

    .pdf-severity-critical {
    background-color: #b23b32;
    }

    .pdf-severity-warning {
    background-color: #b5791f;
    }

    .pdf-severity-notice {
    background-color: #5b6270;
    }

    .pdf-severity-legend td {
    border: none;
    padding: 2px 6px;
    }

    .pdf-legend-swatch {
    width: 12px;
    height: 10px;
    }
@endpush

@section('content')
    @include('audit.pdf.partials.header', ['header' => $header, 'content' => $content])

    @include('audit.pdf.partials.scores', ['content' => $content])

    @include('audit.pdf.partials.charts', ['content' => $content])

    @include('audit.pdf.partials.recommendations', ['content' => $content])

    @include('audit.pdf.partials.summary', ['content' => $content])
@endsection