<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\ReviewPresence;

use App\Audit\ReviewPresence\ReviewPresenceScanner;
use PHPUnit\Framework\TestCase;
use Tests\Support\CrawledPageFactory;

final class ReviewPresenceScannerTest extends TestCase
{
    private function scanner(): ReviewPresenceScanner
    {
        return new ReviewPresenceScanner;
    }

    public function test_a_site_with_no_review_platform_links_reports_every_platform_as_null(): void
    {
        $page = CrawledPageFactory::make();

        $result = $this->scanner()->scan([$page]);

        $this->assertSame(
            ['clutch' => null, 'g2' => null, 'goodfirms' => null, 'google' => null],
            $result->platforms,
        );
        $this->assertSame(
            ['clutch' => null, 'g2' => null, 'goodfirms' => null, 'google' => null],
            $result->platformSourcePages,
        );
    }

    public function test_a_clutch_profile_link_is_detected(): void
    {
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://clutch.co/profile/example-agency'),
        ]);

        $result = $this->scanner()->scan([$page]);

        $this->assertSame('https://clutch.co/profile/example-agency', $result->platforms['clutch']);
    }

    public function test_a_g2_products_link_is_detected(): void
    {
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://www.g2.com/products/example'),
        ]);

        $result = $this->scanner()->scan([$page]);

        $this->assertSame('https://www.g2.com/products/example', $result->platforms['g2']);
    }

    public function test_a_goodfirms_link_is_detected(): void
    {
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://www.goodfirms.co/company/example'),
        ]);

        $result = $this->scanner()->scan([$page]);

        $this->assertSame('https://www.goodfirms.co/company/example', $result->platforms['goodfirms']);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function googleLinkProvider(): array
    {
        return [
            'g.page short link' => ['https://g.page/example-co'],
            'maps.google.com' => ['https://maps.google.com/?cid=123'],
            'google.com/maps' => ['https://www.google.com/maps/place/Example'],
            'business.google.com' => ['https://business.google.com/example'],
        ];
    }

    /**
     * @dataProvider googleLinkProvider
     */
    public function test_google_business_profile_and_maps_links_are_detected(string $url): void
    {
        $page = CrawledPageFactory::make(anchors: [CrawledPageFactory::anchor($url)]);

        $result = $this->scanner()->scan([$page]);

        $this->assertSame($url, $result->platforms['google']);
    }

    public function test_it_never_reports_review_ratings_or_counts_only_presence(): void
    {
        // ReviewPresenceScanner is presence-detection only — it must
        // never fetch/report review data from these platforms' APIs.
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://clutch.co/profile/example-agency'),
        ]);

        $result = $this->scanner()->scan([$page]);

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('rating', $decoded);
        $this->assertArrayNotHasKey('review_count', $decoded);
    }

    public function test_the_first_matching_link_wins_for_a_given_platform(): void
    {
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://clutch.co/profile/first'),
            CrawledPageFactory::anchor('https://clutch.co/profile/second'),
        ]);

        $result = $this->scanner()->scan([$page]);

        $this->assertSame('https://clutch.co/profile/first', $result->platforms['clutch']);
    }

    public function test_result_url_is_the_first_crawled_pages_url(): void
    {
        $home = CrawledPageFactory::make(url: 'https://example.com/');
        $about = CrawledPageFactory::make(url: 'https://example.com/about');

        $result = $this->scanner()->scan([$home, $about]);

        $this->assertSame('https://example.com/', $result->url);
    }

    public function test_the_source_page_is_recorded_for_a_detected_platform_link(): void
    {
        $home = CrawledPageFactory::make(url: 'https://example.com/');
        $about = CrawledPageFactory::make(url: 'https://example.com/about', anchors: [
            CrawledPageFactory::anchor('https://clutch.co/profile/example-agency'),
        ]);

        $result = $this->scanner()->scan([$home, $about]);

        $this->assertSame('https://clutch.co/profile/example-agency', $result->platforms['clutch']);
        $this->assertSame('https://example.com/about', $result->platformSourcePages['clutch']);
        $this->assertNull($result->platformSourcePages['g2']);
    }

    public function test_source_page_follows_the_same_first_match_wins_rule_as_the_link_itself(): void
    {
        $first = CrawledPageFactory::make(url: 'https://example.com/press', anchors: [
            CrawledPageFactory::anchor('https://clutch.co/profile/first'),
        ]);
        $second = CrawledPageFactory::make(url: 'https://example.com/about', anchors: [
            CrawledPageFactory::anchor('https://clutch.co/profile/second'),
        ]);

        $result = $this->scanner()->scan([$first, $second]);

        $this->assertSame('https://clutch.co/profile/first', $result->platforms['clutch']);
        $this->assertSame('https://example.com/press', $result->platformSourcePages['clutch']);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $page = CrawledPageFactory::make();

        $result = $this->scanner()->scan([$page]);

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'platforms', 'platform_source_pages', 'analyzed_at'], array_keys($decoded));
    }
}
