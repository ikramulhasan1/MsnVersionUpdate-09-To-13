<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin-top: 18px; margin-bottom: 6px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .muted { color: #666; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .ok { background: #d1e7dd; color: #0a3622; }
        .warn { background: #fff3cd; color: #664d03; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        td, th { padding: 4px 6px; border-bottom: 1px solid #eee; text-align: left; font-size: 10px; }
    </style>
</head>
<body>
    <h1>On-Page SEO Report</h1>
    <p class="muted">{{ $url }} &mdash; generated {{ now()->format('M j, Y') }}</p>

    <h2>Title Tag</h2>
    <p>{{ $result->title['text'] ?? '(missing)' }}</p>
    <p class="muted">{{ $result->title['length'] }} characters &middot; Status: {{ $result->title['status'] }}</p>

    <h2>Meta Description</h2>
    <p>{{ $result->metaDescription['text'] ?? '(missing)' }}</p>
    <p class="muted">{{ $result->metaDescription['length'] }} characters &middot; Status: {{ $result->metaDescription['status'] }}</p>

    <h2>Headings</h2>
    <p>H1 count: {{ $result->headings['h1_count'] }}</p>
    <table>
        @foreach ($result->headings['hierarchy'] as $heading)
            <tr><td>H{{ $heading['level'] }}</td><td>{{ $heading['text'] }}</td></tr>
        @endforeach
    </table>

    <h2>Content</h2>
    <p>
        Word count: {{ $result->content['word_count'] }} &middot;
        Readability: {{ $result->content['readability_score'] }} ({{ $result->content['readability_label'] }}) &middot;
        Content-to-HTML ratio: {{ $result->content['content_to_html_ratio'] }}%
    </p>

    <h2>Images</h2>
    <p>{{ $result->images['with_alt'] }}/{{ $result->images['total'] }} have alt text ({{ $result->images['without_alt_percent'] }}% missing)</p>

    <h2>Links</h2>
    <p>{{ $result->links['internal_count'] }} internal, {{ $result->links['external_count'] }} external</p>

    <h2>Canonical &amp; Robots</h2>
    <p>Canonical: {{ $result->canonical['canonical'] ?? '(none)' }}</p>
    <p>Robots: {{ $result->canonical['robots'] ?? '(default)' }}</p>

    <h2>Structured Data</h2>
    <p>{{ count($result->schema['types']) > 0 ? implode(', ', $result->schema['types']) : 'None found' }}</p>

    @if ($result->keywordOptimization !== null)
        <h2>Keyword Optimization &mdash; "{{ $result->keywordOptimization['keyword'] }}"</h2>
        <p>Score: {{ $result->keywordOptimization['score'] }}/100</p>
    @endif

    @if ($aiResult !== null && isset($aiResult->recommendations['issue_priority']))
        <h2>Priority Fix List</h2>
        <table>
            <tr><th>Severity</th><th>Issue</th><th>Recommendation</th></tr>
            @foreach ($aiResult->recommendations['issue_priority']['items'] as $item)
                <tr>
                    <td>{{ ucfirst($item['severity'] ?? 'notice') }}</td>
                    <td>{{ $item['message'] ?? '' }}</td>
                    <td>{{ $item['recommendation'] ?? '' }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</body>
</html>