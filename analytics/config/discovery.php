<?php

declare(strict_types=1);

use App\Discovery\Sources\GooglePlacesSource;
use App\Discovery\Sources\InternalCrawlSource;
use App\Discovery\Sources\YelpBusinessSource;

return [

    /*
    |--------------------------------------------------------------------------
    | Industry Taxonomy
    |--------------------------------------------------------------------------
    |
    | Main Industry => [Sub-Niche, Sub-Niche, ...]. This is the data
    | source behind the Website Discovery module's Industry/Niche
    | search — a discovered site's own `industry`/`sub_niche` columns
    | (see database/migrations/2026_08_14_000000_create_discovered_websites_table.php)
    | are expected to be drawn from these same values, so a search
    | filter built from this list actually matches what's stored.
    |
    | App\Discovery\Taxonomy\IndustryTaxonomyService is the only thing
    | that should read this array directly — everywhere else in the app
    | goes through that service, not config('discovery.industries')
    | directly, so this array's own shape can change without every
    | caller needing to change with it.
    |
    | A fixed, hand-curated starter taxonomy — not fetched from or
    | mapped to any external classification standard (e.g. NAICS/SIC
    | codes) — chosen to be immediately useful for a lead-gen/web-audit
    | search UI rather than exhaustive. Extend by adding entries here;
    | nothing else needs to change.
    |
    */
    'industries' => [

        'Restaurant & Food Service' => [
            'Italian', 'Chinese', 'Mexican', 'Japanese & Sushi', 'Fast Food',
            'Cafe & Coffee Shop', 'Bakery', 'Bar & Pub', 'Catering', 'Fine Dining',
            'Food Truck', 'Pizza',
        ],

        'Healthcare & Medical' => [
            'General Practice', 'Dental', 'Dermatology', 'Chiropractic',
            'Physical Therapy', 'Mental Health & Counseling', 'Veterinary',
            'Optometry', 'Pharmacy', 'Urgent Care', 'Pediatrics',
        ],

        'Legal Services' => [
            'Personal Injury', 'Family Law', 'Criminal Defense', 'Corporate Law',
            'Real Estate Law', 'Immigration Law', 'Estate Planning',
            'Bankruptcy Law', 'Employment Law',
        ],

        'Real Estate' => [
            'Residential Sales', 'Commercial Real Estate', 'Property Management',
            'Real Estate Investment', 'Vacation Rentals', 'Mortgage & Lending',
            'Title & Escrow',
        ],

        'Home Services' => [
            'Plumbing', 'Electrical', 'HVAC', 'Roofing', 'Landscaping',
            'Cleaning Services', 'Pest Control', 'Painting', 'General Contracting',
            'Handyman', 'Window & Door',
        ],

        'Automotive' => [
            'Auto Repair', 'Car Dealership', 'Auto Detailing', 'Towing',
            'Auto Parts', 'Tire Shop', 'Auto Body & Collision', 'Car Wash',
        ],

        'Beauty & Personal Care' => [
            'Hair Salon', 'Nail Salon', 'Spa & Massage', 'Barbershop',
            'Skincare & Esthetics', 'Tattoo & Piercing', 'Med Spa',
        ],

        'Fitness & Wellness' => [
            'Gym & Fitness Center', 'Yoga Studio', 'Personal Training', 'CrossFit',
            'Martial Arts', 'Nutrition Coaching', 'Pilates Studio',
        ],

        'Retail & E-commerce' => [
            'Fashion & Apparel', 'Electronics', 'Home Goods', 'Sporting Goods',
            'Toys & Games', 'Jewelry', 'Bookstore', 'Grocery', 'Furniture',
        ],

        'Professional Services' => [
            'Accounting & Bookkeeping', 'Consulting', 'Marketing Agency',
            'IT Services', 'Insurance Agency', 'Financial Advisory', 'HR Services',
            'Web Design Agency',
        ],

        'Education' => [
            'K-12 School', 'Tutoring', 'Online Courses', 'Language School',
            'Music Lessons', 'Test Prep', 'Vocational Training', 'Driving School',
        ],

        'Hospitality & Travel' => [
            'Hotel & Lodging', 'Bed & Breakfast', 'Travel Agency', 'Tour Operator',
            'Event Venue', 'Vacation Rentals',
        ],

        'Construction & Trades' => [
            'General Contractor', 'Home Builder', 'Remodeling', 'Masonry',
            'Flooring', 'Fencing', 'Concrete',
        ],

        'Manufacturing' => [
            'Industrial Equipment', 'Consumer Goods', 'Food Production',
            'Textiles', 'Metal Fabrication', 'Packaging',
        ],

        'Technology & SaaS' => [
            'Software Development', 'SaaS Product', 'Mobile App Development',
            'Web Design Agency', 'IT Support & Managed Services', 'Cybersecurity',
        ],

        'Nonprofit & Community' => [
            'Charity', 'Religious Organization', 'Community Center',
            'Advocacy Group', 'Foundation',
        ],

        'Entertainment & Events' => [
            'Event Planning', 'Photography & Videography', 'DJ & Entertainment',
            'Wedding Services', 'Party Rentals', 'Live Music Venue',
        ],

        'Agriculture' => [
            'Farming', 'Landscaping Supply', 'Nursery & Garden Center',
            'Livestock', 'Agritourism', 'Winery & Vineyard',
        ],

        'Transportation & Logistics' => [
            'Trucking', 'Moving Services', 'Courier & Delivery',
            'Freight Forwarding', 'Taxi & Rideshare', 'Warehousing',
        ],

        'Financial Services' => [
            'Banking', 'Investment Firm', 'Insurance', 'Credit Union',
            'Tax Preparation', 'Payday Lending',
        ],

        'Pet Services' => [
            'Pet Grooming', 'Pet Boarding', 'Dog Walking', 'Pet Training',
            'Pet Store', 'Veterinary',
        ],

        'Childcare & Family' => [
            'Daycare', 'Preschool', 'After-School Programs', 'Babysitting Services',
            'Summer Camp',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Discovery Sources
    |--------------------------------------------------------------------------
    |
    | Every App\Discovery\Sources\Contracts\DiscoverySourceInterface
    | implementation that's actually "live" — every controller/job that
    | needs to go find NEW candidate websites (rather than just search
    | what's already in discovered_websites) resolves each of these
    | classes from the container and calls discover() on it, so this
    | array is the single place that decides which sources are active.
    |
    | Adding a future source (Bing, Clearbit, ...) is exactly two
    | steps, neither of which touches this array's own callers: (1)
    | build a new class implementing DiscoverySourceInterface (see
    | App\Discovery\Sources\GooglePlacesSource or
    | App\Discovery\Sources\YelpBusinessSource for the pattern to
    | follow — API credentials read from config('services.*'), a safe
    | empty-Collection no-op when no credentials are configured yet),
    | (2) list its class-string here.
    |
    | GooglePlacesSource and YelpBusinessSource are both active by
    | default — App\Discovery\Ingestion\DiscoveryIngestionService merges
    | whatever each finds before deduplicating, so a business only one
    | of the two would have surfaced (or would have surfaced WITH a
    | usable website — see GooglePlacesSource's own docblock for why its
    | fill rate on that front is meaningfully higher than Yelp's) still
    | gets found. Each is independently a safe no-op if its own
    | config('services.*') key isn't set yet — having both listed here
    | doesn't require having both configured.
    |
    | InternalCrawlSource (Phase I3) is included alongside them — it
    | crawls outward from whatever discovered_websites already has
    | (including sites the other two sources just added), so keeping it
    | active costs nothing on an empty table (it simply finds no seeds
    | to crawl from yet) and starts pulling its weight the moment
    | there's real data to expand from.
    */

    'sources' => [
        YelpBusinessSource::class,
        InternalCrawlSource::class,
    ],

];
