{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($services as $service)
        <url>
            <loc>{{ route('service.single', $service->slug) }}</loc>

            @if (!empty($service->image_path))
            <image:image>
                <image:loc>{{ asset('uploads/service/'.$service->image_path) }}</image:loc>
                <image:caption>{{ $service->title }}</image:caption>
            </image:image>
            @endif

            @if (!empty($service->subservices))
                @foreach ($service->subservices as $sub)
                    @if (!empty($sub->image_path))
                    <image:image>
                        <image:loc>{{ asset('uploads/service/'.$sub->image_path) }}</image:loc>
                        <image:caption>{{ $sub->title }}</image:caption>
                    </image:image>
                    @endif
                @endforeach
            @endif
        </url>
    @endforeach

    @foreach ($articles as $article)
        <url>
            <loc>{{ route('blog.single', $article->slug) }}</loc>
            <image:image>
            <image:loc>{{ asset('uploads/article/'.$article->image_path) }}</image:loc>
            </image:image>
        </url> 
    @endforeach

</urlset>