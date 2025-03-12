{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    <!-- Sitemap generated dynamically for MSN Softtech services image -->
    @foreach ($services as $service)
        <url>
            <loc>{{ route('service.single', $service->slug) }}</loc>

            @if (!empty($service->image_path))
            <image:image>
                <image:loc>{{ asset('uploads/service/'.$service->image_path) }}</image:loc>
                <image:caption>{{ $service->title }}</image:caption>
            </image:image>
            @endif


        <!-- Sitemap generated dynamically for MSN Softtech SubServices image -->
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

    <!-- Sitemap generated dynamically for MSN Softtech article image -->
    @foreach ($articles as $article)
        <url>
            <loc>{{ route('blog.single', $article->slug) }}</loc>
            <image:image>
                <image:loc>{{ asset('uploads/article/'.$article->image_path) }}</image:loc>
                <image:caption>{{ $article->title }}</image:caption>
            </image:image>
        </url> 
    @endforeach

    <!-- Sitemap generated dynamically for MSN Softtech portfolio image -->
    @foreach ($portfolios as $portfolio)
        <url>
            <loc>{{ route('portfolio.single', $portfolio->slug) }}</loc>
            <image:image>
                <image:loc>{{ asset('uploads/portfolio/'.$portfolio->image_path) }}</image:loc>
                <image:caption>{{ $portfolio->title }}</image:caption>
            </image:image>
        </url> 
    @endforeach

    <!-- Sitemap generated dynamically for MSN Softtech sliders image -->
    @foreach ($sliders as $slider)
        <url>
            <loc>{{ route('home') }}</loc>
            <image:image>
                <image:loc>{{ asset('uploads/slider/'.$slider->image_path) }}</image:loc>
                <image:caption>{{ $slider->title }}</image:caption>
            </image:image>
        </url> 
    @endforeach

    <!-- Sitemap generated dynamically for MSN Softtech clients image -->
    @foreach ($clients as $client)
        <url>
            <loc>{{ route('home') }}</loc>
            <image:image>
                <image:loc>{{ asset('uploads/client/'.$client->image_path) }}</image:loc>
                <image:caption>{{ $client->title }}</image:caption>
            </image:image>
        </url> 
    @endforeach

</urlset>