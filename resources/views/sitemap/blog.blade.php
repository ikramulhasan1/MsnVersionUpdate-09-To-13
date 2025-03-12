{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

    @foreach ($articles as $article)
        <url>
        <loc>{{ route('blog.single',['slug'=>$article->slug]) }}</loc>
        <news:news>
            <news:publication>
            <news:name>{{ env('APP_NAME') }}</news:name>
            <news:language>en</news:language>
            </news:publication>
            <news:publication_date>{{ date("y-m-d", strtotime($article->created_at)) }}</news:publication_date>
            <news:title>{{ $article->title }}</news:title>
        </news:news>
        </url>
    @endforeach
</urlset>