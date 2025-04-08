{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  {{-- <url>
    <loc>{{ URL::to("/") }}</loc>
    <lastmod>2025-05-05</lastmod>
  </url> --}}
  <url>
    <loc>{{ URL::to('/') }}</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/about</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/faqs</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/contact</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/portfolios</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/blogs</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/services</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>
  <url>
    <loc>{{ URL::to('/') }}/get-quote</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
  </url>

  @foreach ($articles as $article)
      <url>
        <loc>{{ route('blog.single',['slug'=>$article->slug]) }}</loc>
        <news:news>
            <news:publication>
                <news:name>{{ env('APP_NAME') }}</news:name>
                <news:language>en</news:language>
            </news:publication>
            <news:publication_date>{{ date("Y-m-d", strtotime($article->created_at)) }}</news:publication_date>
            <news:title>{{ $article->title }}</news:title>
        </news:news>
      </url>
  @endforeach

  @foreach($services as $service)
    <url>
        <loc>{{ htmlspecialchars(route('service.single', ['slug' => $service->slug])) }}</loc>
        <lastmod>{{ $service->updated_at->toDateString() }}</lastmod>
    </url>
  @endforeach
  
  @foreach($portfolios as $portfolio)
    <url>
        <loc>{{ htmlspecialchars(route('portfolio.single', ['slug' => $portfolio->slug])) }}</loc>
        <lastmod>{{ $portfolio->updated_at->toDateString() }}</lastmod>
    </url>
    @endforeach

    @foreach($ArticleCategory as $category)
    <url>
        <loc>{{ htmlspecialchars(route('blog.category', ['slug' => $category->slug])) }}</loc>
        <lastmod>{{ $category->updated_at->toDateString() }}</lastmod>
    </url>
    @endforeach

    @foreach($pages as $page)
    <url>
        <loc>{{ htmlspecialchars(route('page.single', ['slug' => $page->slug])) }}</loc>
        <lastmod>{{ $page->updated_at->toDateString() }}</lastmod>
    </url>
    @endforeach


    {{-- image --}}
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
    @foreach ($technology as $item)
        <url>
            <loc>{{ route('service.technology', $item->slug) }}</loc>

            @if (!empty($item->image_path))
            <image:image>
                <image:loc>{{ asset('uploads/service/'.$item->image_path) }}</image:loc>
                <image:caption>{{ $item->title }}</image:caption>
            </image:image>
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