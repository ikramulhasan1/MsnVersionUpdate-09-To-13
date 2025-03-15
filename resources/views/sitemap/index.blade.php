{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

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

</urlset>
