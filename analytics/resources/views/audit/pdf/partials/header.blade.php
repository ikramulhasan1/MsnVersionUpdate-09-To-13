{{--
    Reusable PDF header: company logo, website URL, audit date.

    A plain include rather than a Blade component, matching this
    project's existing Blade convention (see audit/partials/*.blade.php)
    of @include-based partials over anonymous components.

    Expects a $header variable — an App\Audit\Export\Pdf\DTO\PdfHeaderData
    — from whichever view includes this partial.

    Built as an HTML table rather than floats/flex: dompdf's CSS support
    for floats is inconsistent and it has no flexbox support at all, so
    a table is the reliable way to get a two-column header that renders
    the same in every PDF.
--}}
<table class="pdf-header" role="presentation">
    <tr>
        <td class="pdf-header-logo">
            @if ($header->logoDataUri)
                <img src="{{ $header->logoDataUri }}" alt="{{ $header->companyName }}" class="pdf-header-logo-img">
            @else
                <span class="pdf-header-logo-text">{{ $header->companyName }}</span>
            @endif
        </td>
        <td class="pdf-header-meta">
            <div class="pdf-header-title">Website Audit Report</div>
            <div class="pdf-header-url">{{ $header->websiteUrl }}</div>
            <div class="pdf-header-date">Audit Date: {{ $header->auditDate }}</div>
        </td>
    </tr>
</table>
