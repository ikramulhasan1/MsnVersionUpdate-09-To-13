<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\BusinessSignals;

use App\Audit\BusinessSignals\BusinessSignalsDetector;
use App\Audit\Fetching\DTO\Heading;
use PHPUnit\Framework\TestCase;
use Tests\Support\CrawledPageFactory;

final class BusinessSignalsDetectorTest extends TestCase
{
    private function detector(): BusinessSignalsDetector
    {
        return new BusinessSignalsDetector;
    }

    public function test_plain_pages_with_no_signals_detect_nothing(): void
    {
        $page = CrawledPageFactory::make();

        $result = $this->detector()->analyze($page, [$page]);

        $this->assertSame(
            [
                'careers' => false,
                'hiring' => false,
                'blog_update' => false,
                'funding' => false,
                'new_product' => false,
            ],
            $result->signals,
        );
        $this->assertNull($result->signalDetails['careers']);
        $this->assertNotNull($result->signalDetails['hiring']);
        $this->assertNull($result->signalDetails['blog_update']);
    }

    public function test_funding_and_new_product_always_report_false_with_an_honest_reason(): void
    {
        // Reflects BusinessSignalsDetector's documented scope note: these
        // two require an external data source (e.g. Crunchbase) that
        // does not exist here, so guessing from on-page text would risk
        // a false positive feeding a sales-prioritization score.
        $page = CrawledPageFactory::make(headings: [new Heading(level: 1, text: 'We just raised $10M in funding!')]);

        $result = $this->detector()->analyze($page, [$page]);

        $this->assertFalse($result->signals['funding']);
        $this->assertFalse($result->signals['new_product']);
        $this->assertStringContainsString('external data source', (string) $result->signalDetails['funding']);
        $this->assertStringContainsString('external data source', (string) $result->signalDetails['new_product']);
    }

    public function test_job_posting_schema_detects_both_careers_and_hiring(): void
    {
        $page = CrawledPageFactory::make(
            url: 'https://example.com/careers/backend-engineer',
            schema: [CrawledPageFactory::schemaBlock(['JobPosting'], ['@type' => 'JobPosting', 'title' => 'Backend Engineer'])],
        );

        $result = $this->detector()->analyze($page, [$page]);

        $this->assertTrue($result->signals['careers']);
        $this->assertTrue($result->signals['hiring']);
        $this->assertStringContainsString('JobPosting schema', (string) $result->signalDetails['careers']);
        $this->assertStringContainsString('JobPosting', (string) $result->signalDetails['hiring']);
    }

    public function test_a_careers_shaped_url_without_job_posting_schema_detects_careers_but_not_hiring(): void
    {
        // A careers *page* existing is a weaker claim than active
        // listings — hiring requires the stronger JobPosting-schema
        // signal, per BusinessSignalsDetector's own docblock.
        $page = CrawledPageFactory::make(url: 'https://example.com/careers');

        $result = $this->detector()->analyze($page, [$page]);

        $this->assertTrue($result->signals['careers']);
        $this->assertFalse($result->signals['hiring']);
        $this->assertStringContainsString('Careers-related URL', (string) $result->signalDetails['careers']);
        $this->assertNotNull($result->signalDetails['hiring']);
    }

    public function test_a_careers_shaped_heading_detects_careers(): void
    {
        $home = CrawledPageFactory::make(
            url: 'https://example.com/',
            headings: [new Heading(level: 2, text: "We're hiring across the team")],
        );

        $result = $this->detector()->analyze($home, [$home]);

        $this->assertTrue($result->signals['careers']);
        $this->assertStringContainsString('heading', (string) $result->signalDetails['careers']);
    }

    public function test_a_careers_shaped_link_text_detects_careers(): void
    {
        $home = CrawledPageFactory::make(
            url: 'https://example.com/',
            anchors: [CrawledPageFactory::anchor('https://example.com/jobs', 'Join our team')],
        );

        $result = $this->detector()->analyze($home, [$home]);

        $this->assertTrue($result->signals['careers']);
        $this->assertStringContainsString('link', (string) $result->signalDetails['careers']);
    }

    public function test_a_recent_blog_post_date_detects_blog_update(): void
    {
        $recent = (new \DateTimeImmutable)->modify('-5 days')->format('Y-m-d');

        $blogPage = CrawledPageFactory::make(
            url: 'https://example.com/blog/new-release',
            schema: [CrawledPageFactory::schemaBlock(
                ['BlogPosting'],
                ['@type' => 'BlogPosting', 'datePublished' => $recent],
            )],
        );

        $result = $this->detector()->analyze($blogPage, [$blogPage]);

        $this->assertTrue($result->signals['blog_update']);
        $this->assertStringContainsString($recent, (string) $result->signalDetails['blog_update']);
    }

    public function test_a_stale_blog_post_date_does_not_detect_blog_update_but_still_notes_the_section_exists(): void
    {
        $stale = (new \DateTimeImmutable)->modify('-400 days')->format('Y-m-d');

        $blogPage = CrawledPageFactory::make(
            url: 'https://example.com/blog/old-post',
            schema: [CrawledPageFactory::schemaBlock(
                ['BlogPosting'],
                ['@type' => 'BlogPosting', 'datePublished' => $stale],
            )],
        );

        $result = $this->detector()->analyze($blogPage, [$blogPage]);

        $this->assertFalse($result->signals['blog_update']);
        $this->assertNotNull($result->signalDetails['blog_update']);
    }

    public function test_a_blog_url_with_no_parseable_date_does_not_detect_blog_update_but_still_notes_the_section_was_found(): void
    {
        $blogPage = CrawledPageFactory::make(url: 'https://example.com/blog/no-date-post');

        $result = $this->detector()->analyze($blogPage, [$blogPage]);

        $this->assertFalse($result->signals['blog_update']);
        $this->assertNotNull($result->signalDetails['blog_update']);
        $this->assertStringContainsString('blog/news section was found', (string) $result->signalDetails['blog_update']);
    }

    public function test_no_blog_section_at_all_reports_no_evidence(): void
    {
        // Genuinely nothing blog-shaped crawled — this is the only case
        // where blog_update's detail is null, as opposed to "found but
        // undated" above.
        $page = CrawledPageFactory::make(url: 'https://example.com/about');

        $result = $this->detector()->analyze($page, [$page]);

        $this->assertFalse($result->signals['blog_update']);
        $this->assertNull($result->signalDetails['blog_update']);
    }

    public function test_a_blog_date_nested_inside_a_graph_array_is_still_found(): void
    {
        $recent = (new \DateTimeImmutable)->modify('-1 day')->format('Y-m-d');

        $blogPage = CrawledPageFactory::make(
            url: 'https://example.com/blog/graph-post',
            schema: [CrawledPageFactory::schemaBlock(
                ['Article'],
                ['@graph' => [['@type' => 'Article', 'dateModified' => $recent]]],
            )],
        );

        $result = $this->detector()->analyze($blogPage, [$blogPage]);

        $this->assertTrue($result->signals['blog_update']);
    }

    public function test_result_url_is_the_page_argument_not_the_crawled_pages_list(): void
    {
        $mainPage = CrawledPageFactory::make(url: 'https://example.com/about');
        $otherPage = CrawledPageFactory::make(url: 'https://example.com/careers');

        $result = $this->detector()->analyze($mainPage, [$mainPage, $otherPage]);

        $this->assertSame('https://example.com/about', $result->url);
        // Careers is still detected because analyze() scans every
        // crawled page, not just $page.
        $this->assertTrue($result->signals['careers']);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $page = CrawledPageFactory::make();

        $result = $this->detector()->analyze($page, [$page]);

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'signals', 'signal_details', 'analyzed_at'], array_keys($decoded));
        $this->assertSame(
            ['careers', 'hiring', 'blog_update', 'funding', 'new_product'],
            array_keys($decoded['signals']),
        );
    }
}
