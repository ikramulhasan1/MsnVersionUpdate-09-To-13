{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    @foreach($ArticleCategory as $category)
    <url>
        <loc>{{ htmlspecialchars(route('blog.category', ['slug' => $category->slug])) }}</loc>
        <lastmod>{{ $category->updated_at->toDateString() }}</lastmod>
    </url>
    @endforeach
</urlset>