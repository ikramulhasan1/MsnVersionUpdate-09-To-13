{{--
    Website Discovery — Phase H2 (Export).

    A standalone view (NOT extending audit/pdf/layout.blade.php — that
    shared layout's footer is hardcoded "Website Audit Report", which
    would mislabel this file; a bulk data-dump export also has no need
    for that layout's full "certificate" styling) — a plain, dense,
    dompdf-safe table of every DiscoveryExportRow column, matching
    Excel/CSV/JSON's own identical column set
    (App\Discovery\Export\DiscoveredWebsitesToExportRows is the single
    source of row data every export format reads from).

    Expects:
      $rows   \Illuminate\Support\Collection<int, App\Discovery\Export\DTO\DiscoveryExportRow>
--}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: #15181f;
        }

        h1 {
            font-size: 14px;
            margin-bottom: 4px;
        }

        p.meta {
            font-size: 9px;
            color: #5b6270;
            margin-top: 0;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #e3e1d9;
            padding: 3px 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #7c5f2c;
            color: #ffffff;
            font-weight: bold;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <h1>Discovered Websites Export</h1>
    <p class="meta">{{ count($rows) }} website(s) &mdash; generated {{ now()->format('M j, Y g:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>Business Name</th>
                <th>Website</th>
                <th>Industry</th>
                <th>Country</th>
                <th>City</th>
                <th>Technology</th>
                <th>CMS</th>
                <th>SEO</th>
                <th>Perf</th>
                <th>Sec</th>
                <th>A11y</th>
                <th>Mobile</th>
                <th>Opp.</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Social Links</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->businessName }}</td>
                    <td>{{ $row->website }}</td>
                    <td>{{ $row->industry }}</td>
                    <td>{{ $row->country }}</td>
                    <td>{{ $row->city }}</td>
                    <td>{{ $row->technology }}</td>
                    <td>{{ $row->cms }}</td>
                    <td>{{ $row->seoScore }}</td>
                    <td>{{ $row->performanceScore }}</td>
                    <td>{{ $row->securityScore }}</td>
                    <td>{{ $row->accessibilityScore }}</td>
                    <td>{{ $row->mobileScore }}</td>
                    <td>{{ $row->opportunityScore }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->phone }}</td>
                    <td>{{ $row->socialLinks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
