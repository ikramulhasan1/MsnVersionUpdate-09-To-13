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
--}}
@extends('audit.pdf.layout')

@section('title', 'Website Audit Report - ' . $header->websiteUrl)

@push('styles')
    /* ==========================================================
    Header (Part 1) — untouched, plus one overflow-safety rule
    (long URLs must wrap instead of pushing past the page edge).
    ========================================================== */
    .pdf-header {
    width: 100%;
    border-bottom: 2px solid #2F5496;
    padding-bottom: 12px;
    margin-bottom: 24px;
    }

    .pdf-header-logo {
    width: 140px;
    vertical-align: middle;
    }

    .pdf-header-logo-img {
    max-width: 130px;
    max-height: 60px;
    }

    .pdf-header-logo-text {
    font-size: 18px;
    font-weight: bold;
    color: #2F5496;
    }

    .pdf-header-meta {
    text-align: right;
    vertical-align: middle;
    word-wrap: break-word;
    overflow-wrap: break-word;
    }

    .pdf-header-title {
    font-size: 17px;
    font-weight: bold;
    color: #212529;
    }

    .pdf-header-url {
    font-size: 12px;
    color: #495057;
    margin-top: 3px;
    }

    .pdf-header-date {
    font-size: 11px;
    color: #6c757d;
    margin-top: 3px;
    }

    /* ==========================================================
    Section shell — shared by Scores, Charts, Recommendations
    and Summary. A left accent bar + title gives each section a
    clear, consistent visual start; page-break-inside: avoid on
    the title bar keeps a heading from ever landing alone at the
    bottom of a page.
    ========================================================== */
    .pdf-section {
    margin-bottom: 26px;
    }

    .pdf-section-title-bar {
    border-left: 3px solid #2F5496;
    padding: 2px 0 2px 10px;
    margin: 0 0 10px;
    page-break-inside: avoid;
    page-break-after: avoid;
    }

    .pdf-section-title {
    font-size: 14px;
    font-weight: bold;
    color: #2F5496;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin: 0;
    }

    .pdf-section-title-spaced {
    margin-top: 22px;
    }

    .pdf-empty {
    font-size: 10.5px;
    color: #6c757d;
    font-style: italic;
    margin: 0 0 4px;
    }

    /* ==========================================================
    Tables — Scores, Recommendations, Summary. Fixed layout +
    column widths (set per-table via colgroup in each partial)
    is what guarantees a table never overflows the page width no
    matter how long a URL or issue description is; word-wrap
    (set globally in layout.blade.php) handles the rest.
    ========================================================== */
    .pdf-table {
    font-size: 10px;
    margin-bottom: 4px;
    }

    .pdf-table th,
    .pdf-table td {
    border: 1px solid #d9d9d9;
    padding: 5px 7px;
    text-align: left;
    vertical-align: top;
    }

    .pdf-table thead th {
    background-color: #2F5496;
    color: #ffffff;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    }

    .pdf-table tbody tr:nth-child(even) {
    background-color: #f7f9fc;
    }

    /* Compact "Page URL" columns (Recommendations) — smaller and
    muted so a long URL doesn't visually compete with the issue/
    recommendation text next to it. */
    .pdf-table-small {
    font-size: 8.5px;
    color: #6c757d;
    word-break: break-all;
    }

    .pdf-summary-table th {
    background-color: #f2f2f2;
    font-weight: bold;
    white-space: nowrap;
    }

    /* ==========================================================
    Charts — a table-based bar chart (percentage column widths,
    not fixed pixel widths) so it resizes safely with the page
    instead of risking overflow, plus the severity legend.
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
    }

    .pdf-chart-track-cell {
    width: 56%;
    }

    .pdf-chart-value {
    width: 20%;
    font-size: 10.5px;
    text-align: right;
    }

    .pdf-bar-track {
    width: 100%;
    height: 10px;
    background-color: #eef1f6;
    border: 1px solid #dfe4ee;
    }

    .pdf-bar-fill {
    height: 8px;
    margin-top: 1px;
    background-color: #2F5496;
    }

    .pdf-severity-bar {
    width: 100%;
    height: 14px;
    margin-bottom: 10px;
    border: 1px solid #dfe4ee;
    }

    .pdf-severity-segment {
    display: inline-block;
    height: 14px;
    }

    .pdf-severity-critical {
    background-color: #C0392B;
    }

    .pdf-severity-warning {
    background-color: #E1A100;
    }

    .pdf-severity-notice {
    background-color: #4A90D9;
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
    @include('audit.pdf.partials.header', ['header' => $header])

    @include('audit.pdf.partials.scores', ['content' => $content])

    @include('audit.pdf.partials.charts', ['content' => $content])

    @include('audit.pdf.partials.recommendations', ['content' => $content])

    @include('audit.pdf.partials.summary', ['content' => $content])
@endsection
