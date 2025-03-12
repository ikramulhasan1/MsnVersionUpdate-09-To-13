{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    @foreach($services as $service)
    <url>
        <loc>{{ htmlspecialchars(route('service.single', ['slug' => $service->slug])) }}</loc>
        <lastmod>{{ $service->updated_at->toDateString() }}</lastmod>
    </url>
    @endforeach
</urlset>