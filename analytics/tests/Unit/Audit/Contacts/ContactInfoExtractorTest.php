<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Contacts;

use App\Audit\Contacts\ContactInfoExtractor;
use PHPUnit\Framework\TestCase;
use Tests\Support\CrawledPageFactory;

final class ContactInfoExtractorTest extends TestCase
{
    private function extractor(): ContactInfoExtractor
    {
        return new ContactInfoExtractor;
    }

    public function test_result_url_is_the_first_crawled_pages_url(): void
    {
        $home = CrawledPageFactory::make(url: 'https://example.com/');
        $about = CrawledPageFactory::make(url: 'https://example.com/about');

        $result = $this->extractor()->extract([$home, $about]);

        $this->assertSame('https://example.com/', $result->url);
    }

    public function test_empty_crawled_pages_produce_an_empty_result_with_an_empty_url(): void
    {
        $result = $this->extractor()->extract([]);

        $this->assertSame('', $result->url);
        $this->assertSame([], $result->emails);
        $this->assertSame([], $result->phones);
        $this->assertSame([], $result->socialProfiles);
        $this->assertSame([], $result->teamMembers);
    }

    public function test_mailto_emails_are_collected_lowercased_and_deduplicated(): void
    {
        $page = CrawledPageFactory::make(mailtoLinks: ['Sales@Acme-Co.com', 'sales@acme-co.com', 'hi@acme-co.com']);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame(
            ['sales@acme-co.com', 'hi@acme-co.com'],
            array_column($result->emails, 'value'),
        );
    }

    public function test_each_email_records_the_page_it_was_found_on(): void
    {
        $page = CrawledPageFactory::make(url: 'https://example.com/contact', mailtoLinks: ['sales@acme-co.com']);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame('sales@acme-co.com', $result->emails[0]['value']);
        $this->assertSame('https://example.com/contact', $result->emails[0]['sourceUrl']);
    }

    public function test_an_email_repeated_across_pages_keeps_the_first_pages_source_url(): void
    {
        $home = CrawledPageFactory::make(url: 'https://example.com/', mailtoLinks: ['sales@acme-co.com']);
        $contact = CrawledPageFactory::make(url: 'https://example.com/contact', mailtoLinks: ['sales@acme-co.com']);

        $result = $this->extractor()->extract([$home, $contact]);

        $this->assertCount(1, $result->emails);
        $this->assertSame('https://example.com/', $result->emails[0]['sourceUrl']);
    }

    public function test_any_address_on_the_example_com_or_test_com_domain_is_treated_as_a_placeholder(): void
    {
        // Not just the exact example@example.com — the whole domain is
        // treated as a placeholder, since real prospects never actually
        // publish a contact address on it.
        $page = CrawledPageFactory::make(mailtoLinks: ['sales@example.com', 'hi@test.com']);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame([], $result->emails);
    }

    public function test_placeholder_and_invalid_emails_are_excluded(): void
    {
        $page = CrawledPageFactory::make(mailtoLinks: [
            'example@example.com',
            'test@test.com',
            'not-an-email',
            'real@company.com',
        ]);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame(['real@company.com'], array_column($result->emails, 'value'));
    }

    public function test_tel_links_are_collected_verbatim_and_deduplicated(): void
    {
        $page = CrawledPageFactory::make(telLinks: ['+1-555-0100', '+1-555-0100', '555.0200']);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame(['+1-555-0100', '555.0200'], array_column($result->phones, 'value'));
    }

    public function test_each_phone_records_the_page_it_was_found_on(): void
    {
        $page = CrawledPageFactory::make(url: 'https://example.com/contact', telLinks: ['+1-555-0100']);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame('+1-555-0100', $result->phones[0]['value']);
        $this->assertSame('https://example.com/contact', $result->phones[0]['sourceUrl']);
    }

    public function test_a_phone_repeated_across_pages_keeps_the_first_pages_source_url(): void
    {
        $home = CrawledPageFactory::make(url: 'https://example.com/', telLinks: ['+1-555-0100']);
        $contact = CrawledPageFactory::make(url: 'https://example.com/contact', telLinks: ['+1-555-0100']);

        $result = $this->extractor()->extract([$home, $contact]);

        $this->assertCount(1, $result->phones);
        $this->assertSame('https://example.com/', $result->phones[0]['sourceUrl']);
    }

    public function test_social_profile_links_are_detected_per_platform(): void
    {
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://www.linkedin.com/company/example-co'),
            CrawledPageFactory::anchor('https://facebook.com/example'),
            CrawledPageFactory::anchor('https://instagram.com/example'),
            CrawledPageFactory::anchor('https://x.com/example'),
            CrawledPageFactory::anchor('https://youtube.com/@example'),
        ]);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame([
            'linkedin' => 'https://www.linkedin.com/company/example-co',
            'facebook' => 'https://facebook.com/example',
            'instagram' => 'https://instagram.com/example',
            'twitter' => 'https://x.com/example',
            'youtube' => 'https://youtube.com/@example',
        ], $result->socialProfiles);
    }

    public function test_the_first_matching_link_wins_for_a_given_platform(): void
    {
        $page = CrawledPageFactory::make(anchors: [
            CrawledPageFactory::anchor('https://facebook.com/first'),
            CrawledPageFactory::anchor('https://facebook.com/second'),
        ]);

        $result = $this->extractor()->extract([$page]);

        $this->assertSame('https://facebook.com/first', $result->socialProfiles['facebook']);
    }

    public function test_a_platform_with_no_matching_link_is_omitted_entirely(): void
    {
        $page = CrawledPageFactory::make(anchors: [CrawledPageFactory::anchor('https://facebook.com/example')]);

        $result = $this->extractor()->extract([$page]);

        $this->assertArrayHasKey('facebook', $result->socialProfiles);
        $this->assertArrayNotHasKey('linkedin', $result->socialProfiles);
    }

    public function test_team_members_are_extracted_from_person_schema_on_a_team_shaped_page(): void
    {
        $teamPage = CrawledPageFactory::make(
            url: 'https://example.com/about/team',
            schema: [CrawledPageFactory::schemaBlock(
                ['Person'],
                ['@type' => 'Person', 'name' => 'Jamie Rivera', 'jobTitle' => 'Head of Sales'],
            )],
        );

        $result = $this->extractor()->extract([$teamPage]);

        $this->assertCount(1, $result->teamMembers);
        $this->assertSame('Jamie Rivera', $result->teamMembers[0]['name']);
        $this->assertSame('Head of Sales', $result->teamMembers[0]['title']);
        $this->assertSame('https://example.com/about/team', $result->teamMembers[0]['sourceUrl']);
    }

    public function test_person_schema_on_a_non_team_page_is_not_extracted_as_a_team_member(): void
    {
        // Only pages whose URL/title clearly signal a team/about page are
        // scanned — per ContactInfoExtractor's docblock, guessing team
        // membership from an arbitrary page risks attributing a role to
        // the wrong named person.
        $blogPage = CrawledPageFactory::make(
            url: 'https://example.com/blog/2026-recap',
            schema: [CrawledPageFactory::schemaBlock(
                ['Person'],
                ['@type' => 'Person', 'name' => 'Jamie Rivera'],
            )],
        );

        $result = $this->extractor()->extract([$blogPage]);

        $this->assertSame([], $result->teamMembers);
    }

    public function test_a_person_node_with_no_name_is_skipped_rather_than_guessed(): void
    {
        $teamPage = CrawledPageFactory::make(
            url: 'https://example.com/team',
            schema: [CrawledPageFactory::schemaBlock(['Person'], ['@type' => 'Person'])],
        );

        $result = $this->extractor()->extract([$teamPage]);

        $this->assertSame([], $result->teamMembers);
    }

    public function test_linkedin_url_is_picked_out_of_same_as_when_present(): void
    {
        $teamPage = CrawledPageFactory::make(
            url: 'https://example.com/team',
            schema: [CrawledPageFactory::schemaBlock(
                ['Person'],
                [
                    '@type' => 'Person',
                    'name' => 'Jamie Rivera',
                    'sameAs' => ['https://twitter.com/jamie', 'https://www.linkedin.com/in/jamierivera'],
                ],
            )],
        );

        $result = $this->extractor()->extract([$teamPage]);

        $this->assertSame('https://www.linkedin.com/in/jamierivera', $result->teamMembers[0]['linkedinUrl']);
    }

    public function test_person_nodes_nested_in_a_graph_array_are_still_found(): void
    {
        $teamPage = CrawledPageFactory::make(
            url: 'https://example.com/our-people',
            schema: [CrawledPageFactory::schemaBlock(
                ['Organization'],
                ['@graph' => [['@type' => 'Person', 'name' => 'Alex Chen']]],
            )],
        );

        $result = $this->extractor()->extract([$teamPage]);

        $this->assertCount(1, $result->teamMembers);
        $this->assertSame('Alex Chen', $result->teamMembers[0]['name']);
    }

    public function test_the_same_name_on_the_same_page_is_not_duplicated(): void
    {
        $teamPage = CrawledPageFactory::make(
            url: 'https://example.com/team',
            schema: [
                CrawledPageFactory::schemaBlock(['Person'], ['@type' => 'Person', 'name' => 'Jamie Rivera']),
                CrawledPageFactory::schemaBlock(['Person'], ['@type' => 'Person', 'name' => 'Jamie Rivera']),
            ],
        );

        $result = $this->extractor()->extract([$teamPage]);

        $this->assertCount(1, $result->teamMembers);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $page = CrawledPageFactory::make();

        $result = $this->extractor()->extract([$page]);

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['url', 'emails', 'phones', 'social_profiles', 'team_members', 'analyzed_at'],
            array_keys($decoded),
        );
    }

    public function test_email_and_phone_entries_serialize_with_value_and_source_url(): void
    {
        $page = CrawledPageFactory::make(
            url: 'https://example.com/contact',
            mailtoLinks: ['sales@acme-co.com'],
            telLinks: ['+1-555-0100'],
        );

        $result = $this->extractor()->extract([$page]);

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['value' => 'sales@acme-co.com', 'sourceUrl' => 'https://example.com/contact'],
            $decoded['emails'][0],
        );
        $this->assertSame(
            ['value' => '+1-555-0100', 'sourceUrl' => 'https://example.com/contact'],
            $decoded['phones'][0],
        );
    }
}
