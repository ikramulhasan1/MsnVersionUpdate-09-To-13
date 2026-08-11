{{--
    Base layout for every PDF export view.

    Kept as a separate, reusable layout (rather than baked into
    report.blade.php) so any future PDF export in this module — not
    just the audit report — can @extend it and get the same A4 page
    setup, typography, print rules and page-numbered footer for free.

    Part 3 (Prompt 17.3) finalizes this layout: base typography (font,
    sizing, line-height, heading rhythm), print-safety rules that apply
    to every table in the document (fixed layout, word-wrapping,
    repeating <thead> across page breaks, no orphaned rows), and the
    fixed footer that gives every page a "Page X of Y" number. None of
    this changes what any partial renders — it only changes how it's
    allowed to lay out and break across pages.

    CSS here is deliberately dompdf-safe: no flexbox/grid, no external
    stylesheet, 'DejaVu Sans' for reliable Unicode rendering. Section-
    specific styles are pushed onto the 'styles' stack by the view that
    extends this layout, so this file never needs to change as new
    sections are added.
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Website Audit Report')</title>
    <style>
        /* A4 page with room at the bottom for the fixed page-number
           footer, and enough top margin that content never starts
           flush against the page edge. */
        @page {
            size: a4;
            margin: 30px 32px 55px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #212529;
        }

        h1, h2, h3 {
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: bold;
            line-height: 1.3;
            margin: 0 0 8px;
            /* A heading is never left alone at the bottom of a page
               with its content pushed to the next one. */
            page-break-after: avoid;
        }

        p {
            margin: 0 0 8px;
        }

        /* Print-safety rules that apply to every table in the PDF,
           regardless of which partial renders it (Scores, Charts'
           severity legend, Recommendations, Summary):
           - table-layout: fixed + word-wrap keeps a long URL or issue
             description from ever overflowing the page width.
           - thead as its own table-row-group is what lets dompdf
             repeat column headers automatically on every page a table
             spans, so a reader never sees an unlabeled continuation.
           - tr { page-break-inside: avoid } keeps a single row from
             being split across two pages. */
        table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        th, td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Fixed footer, repeated on every page. {PAGE_NUM} / {PAGE_COUNT}
           are dompdf's built-in page-numbering tokens — they only
           resolve inside a `position: fixed` element, which is also
           what makes this element repeat on every page rather than
           appearing once. */
        .pdf-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -40px;
            height: 24px;
            padding-top: 6px;
            border-top: 1px solid #d9d9d9;
            font-size: 9px;
            color: #6c757d;
        }

        .pdf-footer-left {
            display: inline-block;
            width: 70%;
        }

        .pdf-footer-right {
            display: inline-block;
            width: 30%;
            text-align: right;
        }

        @stack('styles')
    </style>
</head>
<body>
    <div class="pdf-footer">
        <span class="pdf-footer-left">{{ config('app.name') }} &mdash; Website Audit Report</span>
        <span class="pdf-footer-right">Page {PAGE_NUM} of {PAGE_COUNT}</span>
    </div>

    @yield('content')
</body>
</html>
