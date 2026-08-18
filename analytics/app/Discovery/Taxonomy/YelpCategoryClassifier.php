<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

/**
 * Maps a business's raw Yelp category text (e.g. "Italian Restaurant",
 * "Men's Clothing") onto this module's own curated Industry/Sub-Niche
 * taxonomy (config('discovery.industries') —
 * App\Discovery\Taxonomy\IndustryTaxonomyService's own source of
 * truth) — this is the actual fix for the real problem: Yelp's own
 * category text almost never matches a curated top-level Industry name
 * verbatim, so App\Discovery\Sources\YelpBusinessSource storing that
 * raw text directly (an earlier, WRONG fix — see
 * IndustryTaxonomyService's own git history / docblock, which briefly
 * made the dropdown read distinct real values instead, before this
 * classifier replaced that approach) meant the search panel's own nice
 * Industry -> Sub-Niche cascade effectively never worked: the dropdown
 * showed curated names, but almost nothing stored on
 * discovered_websites actually matched one.
 *
 * KEYWORD_MAP is intentionally a best-effort, not-exhaustive mapping —
 * built from the curated taxonomy's own ~140 sub-niche names plus
 * common real-world Yelp category synonyms for each. A Yelp category
 * this map genuinely has no entry for classifies to
 * industry: null, subNiche: null — the same "don't fabricate what
 * isn't there" rule this module's other components already follow
 * (see e.g. App\Discovery\Sources\DTO\DiscoveredWebsiteDTO's own
 * docblock) rather than guessing at a bucket that might be wrong. This
 * is a deliberate, ongoing trade-off: some real businesses will have
 * no industry classification until this map is extended to cover
 * their specific Yelp category — extending KEYWORD_MAP with a new
 * keyword => [industry, subNiche] entry is the only change ever needed
 * to improve coverage, no other class needs to change.
 */
final class YelpCategoryClassifier
{
    /**
     * Checked via str_contains() against the LOWERCASED Yelp category
     * title, longest/most-specific keyword first within each industry
     * so e.g. "fast food" matches before a shorter, more generic
     * keyword could. subNiche is null for a handful of deliberately
     * generic/industry-only entries (e.g. plain "restaurant"), which
     * still lets Industry-level filtering work even when no specific
     * Sub-Niche can be determined.
     *
     * @var array<string, array{0: string, 1: ?string}>
     */
    private const array KEYWORD_MAP = [
        // Restaurant & Food Service
        'fast food' => ['Restaurant & Food Service', 'Fast Food'],
        'burger' => ['Restaurant & Food Service', 'Fast Food'],
        'fine dining' => ['Restaurant & Food Service', 'Fine Dining'],
        'food truck' => ['Restaurant & Food Service', 'Food Truck'],
        'food cart' => ['Restaurant & Food Service', 'Food Truck'],
        'sushi' => ['Restaurant & Food Service', 'Japanese & Sushi'],
        'japanese' => ['Restaurant & Food Service', 'Japanese & Sushi'],
        'italian' => ['Restaurant & Food Service', 'Italian'],
        'chinese' => ['Restaurant & Food Service', 'Chinese'],
        'mexican' => ['Restaurant & Food Service', 'Mexican'],
        'taco' => ['Restaurant & Food Service', 'Mexican'],
        'pizza' => ['Restaurant & Food Service', 'Pizza'],
        'coffee' => ['Restaurant & Food Service', 'Cafe & Coffee Shop'],
        'cafe' => ['Restaurant & Food Service', 'Cafe & Coffee Shop'],
        'coffeehouse' => ['Restaurant & Food Service', 'Cafe & Coffee Shop'],
        'bakery' => ['Restaurant & Food Service', 'Bakery'],
        'patisserie' => ['Restaurant & Food Service', 'Bakery'],
        'bar' => ['Restaurant & Food Service', 'Bar & Pub'],
        'pub' => ['Restaurant & Food Service', 'Bar & Pub'],
        'brewery' => ['Restaurant & Food Service', 'Bar & Pub'],
        'wine bar' => ['Restaurant & Food Service', 'Bar & Pub'],
        'cocktail' => ['Restaurant & Food Service', 'Bar & Pub'],
        'caterer' => ['Restaurant & Food Service', 'Catering'],
        'catering' => ['Restaurant & Food Service', 'Catering'],
        'restaurant' => ['Restaurant & Food Service', null],
        'diner' => ['Restaurant & Food Service', null],
        'eatery' => ['Restaurant & Food Service', null],

        // Healthcare & Medical
        'family practice' => ['Healthcare & Medical', 'General Practice'],
        'general practice' => ['Healthcare & Medical', 'General Practice'],
        'internal medicine' => ['Healthcare & Medical', 'General Practice'],
        'dentist' => ['Healthcare & Medical', 'Dental'],
        'dental' => ['Healthcare & Medical', 'Dental'],
        'orthodont' => ['Healthcare & Medical', 'Dental'],
        'dermatolog' => ['Healthcare & Medical', 'Dermatology'],
        'skin doctor' => ['Healthcare & Medical', 'Dermatology'],
        'chiropract' => ['Healthcare & Medical', 'Chiropractic'],
        'physical therap' => ['Healthcare & Medical', 'Physical Therapy'],
        'physiotherap' => ['Healthcare & Medical', 'Physical Therapy'],
        'counsel' => ['Healthcare & Medical', 'Mental Health & Counseling'],
        'therapist' => ['Healthcare & Medical', 'Mental Health & Counseling'],
        'psycholog' => ['Healthcare & Medical', 'Mental Health & Counseling'],
        'psychiatr' => ['Healthcare & Medical', 'Mental Health & Counseling'],
        'optometr' => ['Healthcare & Medical', 'Optometry'],
        'eye care' => ['Healthcare & Medical', 'Optometry'],
        'eyewear' => ['Healthcare & Medical', 'Optometry'],
        'pharmac' => ['Healthcare & Medical', 'Pharmacy'],
        'drugstore' => ['Healthcare & Medical', 'Pharmacy'],
        'urgent care' => ['Healthcare & Medical', 'Urgent Care'],
        'walk-in clinic' => ['Healthcare & Medical', 'Urgent Care'],
        'pediatric' => ['Healthcare & Medical', 'Pediatrics'],
        'medical center' => ['Healthcare & Medical', null],
        'clinic' => ['Healthcare & Medical', null],
        'doctor' => ['Healthcare & Medical', null],

        // Legal Services
        'personal injury' => ['Legal Services', 'Personal Injury'],
        'family law' => ['Legal Services', 'Family Law'],
        'divorce' => ['Legal Services', 'Family Law'],
        'criminal defense' => ['Legal Services', 'Criminal Defense'],
        'corporate law' => ['Legal Services', 'Corporate Law'],
        'real estate law' => ['Legal Services', 'Real Estate Law'],
        'immigration law' => ['Legal Services', 'Immigration Law'],
        'immigration attorney' => ['Legal Services', 'Immigration Law'],
        'estate planning' => ['Legal Services', 'Estate Planning'],
        'bankruptcy' => ['Legal Services', 'Bankruptcy Law'],
        'employment law' => ['Legal Services', 'Employment Law'],
        'attorney' => ['Legal Services', null],
        'lawyer' => ['Legal Services', null],
        'law firm' => ['Legal Services', null],

        // Real Estate
        'real estate agent' => ['Real Estate', 'Residential Sales'],
        'realtor' => ['Real Estate', 'Residential Sales'],
        'commercial real estate' => ['Real Estate', 'Commercial Real Estate'],
        'property management' => ['Real Estate', 'Property Management'],
        'real estate investment' => ['Real Estate', 'Real Estate Investment'],
        'vacation rental' => ['Real Estate', 'Vacation Rentals'],
        'mortgage' => ['Real Estate', 'Mortgage & Lending'],
        'lending' => ['Real Estate', 'Mortgage & Lending'],
        'title compan' => ['Real Estate', 'Title & Escrow'],
        'escrow' => ['Real Estate', 'Title & Escrow'],
        'real estate' => ['Real Estate', null],

        // Home Services
        'plumb' => ['Home Services', 'Plumbing'],
        'electrician' => ['Home Services', 'Electrical'],
        'electrical' => ['Home Services', 'Electrical'],
        'hvac' => ['Home Services', 'HVAC'],
        'air conditioning' => ['Home Services', 'HVAC'],
        'heating' => ['Home Services', 'HVAC'],
        'roofing' => ['Home Services', 'Roofing'],
        'roofer' => ['Home Services', 'Roofing'],
        'landscap' => ['Home Services', 'Landscaping'],
        'lawn care' => ['Home Services', 'Landscaping'],
        'cleaning service' => ['Home Services', 'Cleaning Services'],
        'house cleaning' => ['Home Services', 'Cleaning Services'],
        'maid service' => ['Home Services', 'Cleaning Services'],
        'pest control' => ['Home Services', 'Pest Control'],
        'exterminat' => ['Home Services', 'Pest Control'],
        'painter' => ['Home Services', 'Painting'],
        'painting' => ['Home Services', 'Painting'],
        'general contractor' => ['Home Services', 'General Contracting'],
        'handyman' => ['Home Services', 'Handyman'],
        'window' => ['Home Services', 'Window & Door'],
        'door' => ['Home Services', 'Window & Door'],

        // Automotive
        'auto repair' => ['Automotive', 'Auto Repair'],
        'mechanic' => ['Automotive', 'Auto Repair'],
        'car dealer' => ['Automotive', 'Car Dealership'],
        'auto dealer' => ['Automotive', 'Car Dealership'],
        'auto detailing' => ['Automotive', 'Auto Detailing'],
        'car detailing' => ['Automotive', 'Auto Detailing'],
        'towing' => ['Automotive', 'Towing'],
        'auto parts' => ['Automotive', 'Auto Parts'],
        'tire' => ['Automotive', 'Tire Shop'],
        'auto body' => ['Automotive', 'Auto Body & Collision'],
        'collision' => ['Automotive', 'Auto Body & Collision'],
        'car wash' => ['Automotive', 'Car Wash'],
        'automotive' => ['Automotive', null],

        // Beauty & Personal Care
        'hair salon' => ['Beauty & Personal Care', 'Hair Salon'],
        'hairdresser' => ['Beauty & Personal Care', 'Hair Salon'],
        'nail salon' => ['Beauty & Personal Care', 'Nail Salon'],
        'nail spa' => ['Beauty & Personal Care', 'Nail Salon'],
        'spa' => ['Beauty & Personal Care', 'Spa & Massage'],
        'massage' => ['Beauty & Personal Care', 'Spa & Massage'],
        'barbershop' => ['Beauty & Personal Care', 'Barbershop'],
        'barber' => ['Beauty & Personal Care', 'Barbershop'],
        'skincare' => ['Beauty & Personal Care', 'Skincare & Esthetics'],
        'esthetic' => ['Beauty & Personal Care', 'Skincare & Esthetics'],
        'tattoo' => ['Beauty & Personal Care', 'Tattoo & Piercing'],
        'piercing' => ['Beauty & Personal Care', 'Tattoo & Piercing'],
        'med spa' => ['Beauty & Personal Care', 'Med Spa'],
        'medspa' => ['Beauty & Personal Care', 'Med Spa'],

        // Fitness & Wellness
        'gym' => ['Fitness & Wellness', 'Gym & Fitness Center'],
        'fitness center' => ['Fitness & Wellness', 'Gym & Fitness Center'],
        'yoga' => ['Fitness & Wellness', 'Yoga Studio'],
        'personal train' => ['Fitness & Wellness', 'Personal Training'],
        'crossfit' => ['Fitness & Wellness', 'CrossFit'],
        'martial arts' => ['Fitness & Wellness', 'Martial Arts'],
        'karate' => ['Fitness & Wellness', 'Martial Arts'],
        'jiu-jitsu' => ['Fitness & Wellness', 'Martial Arts'],
        'nutrition' => ['Fitness & Wellness', 'Nutrition Coaching'],
        'pilates' => ['Fitness & Wellness', 'Pilates Studio'],

        // Retail & E-commerce
        "men's clothing" => ['Retail & E-commerce', 'Fashion & Apparel'],
        "women's clothing" => ['Retail & E-commerce', 'Fashion & Apparel'],
        'fashion' => ['Retail & E-commerce', 'Fashion & Apparel'],
        'apparel' => ['Retail & E-commerce', 'Fashion & Apparel'],
        'boutique' => ['Retail & E-commerce', 'Fashion & Apparel'],
        'clothing' => ['Retail & E-commerce', 'Fashion & Apparel'],
        'shoe store' => ['Retail & E-commerce', 'Fashion & Apparel'],
        'electronics' => ['Retail & E-commerce', 'Electronics'],
        'home goods' => ['Retail & E-commerce', 'Home Goods'],
        'home decor' => ['Retail & E-commerce', 'Home Goods'],
        'sporting goods' => ['Retail & E-commerce', 'Sporting Goods'],
        'toy store' => ['Retail & E-commerce', 'Toys & Games'],
        'hobby shop' => ['Retail & E-commerce', 'Toys & Games'],
        'jewelry' => ['Retail & E-commerce', 'Jewelry'],
        'jeweler' => ['Retail & E-commerce', 'Jewelry'],
        'bookstore' => ['Retail & E-commerce', 'Bookstore'],
        'book shop' => ['Retail & E-commerce', 'Bookstore'],
        'grocery' => ['Retail & E-commerce', 'Grocery'],
        'supermarket' => ['Retail & E-commerce', 'Grocery'],
        'furniture' => ['Retail & E-commerce', 'Furniture'],
        'accessories' => ['Retail & E-commerce', null],
        'department store' => ['Retail & E-commerce', null],
        'gift shop' => ['Retail & E-commerce', null],
        'shopping' => ['Retail & E-commerce', null],

        // Professional Services
        'accounting' => ['Professional Services', 'Accounting & Bookkeeping'],
        'bookkeep' => ['Professional Services', 'Accounting & Bookkeeping'],
        'cpa' => ['Professional Services', 'Accounting & Bookkeeping'],
        'consulting' => ['Professional Services', 'Consulting'],
        'consultant' => ['Professional Services', 'Consulting'],
        'marketing agency' => ['Professional Services', 'Marketing Agency'],
        'advertising' => ['Professional Services', 'Marketing Agency'],
        'it services' => ['Professional Services', 'IT Services'],
        'insurance' => ['Professional Services', 'Insurance Agency'],
        'financial advis' => ['Professional Services', 'Financial Advisory'],
        'financial planning' => ['Professional Services', 'Financial Advisory'],
        'hr services' => ['Professional Services', 'HR Services'],
        'staffing' => ['Professional Services', 'HR Services'],
        'web design' => ['Professional Services', 'Web Design Agency'],
        'notary' => ['Professional Services', null],

        // Education
        'elementary school' => ['Education', 'K-12 School'],
        'high school' => ['Education', 'K-12 School'],
        'private school' => ['Education', 'K-12 School'],
        'tutoring' => ['Education', 'Tutoring'],
        'tutor' => ['Education', 'Tutoring'],
        'online course' => ['Education', 'Online Courses'],
        'e-learning' => ['Education', 'Online Courses'],
        'language school' => ['Education', 'Language School'],
        'esl' => ['Education', 'Language School'],
        'music lesson' => ['Education', 'Music Lessons'],
        'music school' => ['Education', 'Music Lessons'],
        'test prep' => ['Education', 'Test Prep'],
        'vocational' => ['Education', 'Vocational Training'],
        'trade school' => ['Education', 'Vocational Training'],
        'driving school' => ['Education', 'Driving School'],

        // Hospitality & Travel
        'hotel' => ['Hospitality & Travel', 'Hotel & Lodging'],
        'motel' => ['Hospitality & Travel', 'Hotel & Lodging'],
        'resort' => ['Hospitality & Travel', 'Hotel & Lodging'],
        'bed & breakfast' => ['Hospitality & Travel', 'Bed & Breakfast'],
        'bed and breakfast' => ['Hospitality & Travel', 'Bed & Breakfast'],
        'travel agency' => ['Hospitality & Travel', 'Travel Agency'],
        'tour operator' => ['Hospitality & Travel', 'Tour Operator'],
        'tour guide' => ['Hospitality & Travel', 'Tour Operator'],
        'event venue' => ['Hospitality & Travel', 'Event Venue'],
        'banquet hall' => ['Hospitality & Travel', 'Event Venue'],

        // Construction & Trades
        'home builder' => ['Construction & Trades', 'Home Builder'],
        'remodel' => ['Construction & Trades', 'Remodeling'],
        'renovation' => ['Construction & Trades', 'Remodeling'],
        'mason' => ['Construction & Trades', 'Masonry'],
        'flooring' => ['Construction & Trades', 'Flooring'],
        'fencing' => ['Construction & Trades', 'Fencing'],
        'fence' => ['Construction & Trades', 'Fencing'],
        'concrete' => ['Construction & Trades', 'Concrete'],
        'construction' => ['Construction & Trades', null],
        'contractor' => ['Construction & Trades', null],

        // Manufacturing
        'industrial equipment' => ['Manufacturing', 'Industrial Equipment'],
        'consumer goods' => ['Manufacturing', 'Consumer Goods'],
        'food production' => ['Manufacturing', 'Food Production'],
        'textile' => ['Manufacturing', 'Textiles'],
        'metal fabrication' => ['Manufacturing', 'Metal Fabrication'],
        'packaging' => ['Manufacturing', 'Packaging'],
        'manufactur' => ['Manufacturing', null],
        'factory' => ['Manufacturing', null],

        // Technology & SaaS
        'software development' => ['Technology & SaaS', 'Software Development'],
        'software compan' => ['Technology & SaaS', 'Software Development'],
        'saas' => ['Technology & SaaS', 'SaaS Product'],
        'mobile app' => ['Technology & SaaS', 'Mobile App Development'],
        'app development' => ['Technology & SaaS', 'Mobile App Development'],
        'it support' => ['Technology & SaaS', 'IT Support & Managed Services'],
        'managed services' => ['Technology & SaaS', 'IT Support & Managed Services'],
        'cybersecurity' => ['Technology & SaaS', 'Cybersecurity'],
        'computer repair' => ['Technology & SaaS', 'IT Support & Managed Services'],

        // Nonprofit & Community
        'charity' => ['Nonprofit & Community', 'Charity'],
        'nonprofit' => ['Nonprofit & Community', 'Charity'],
        'church' => ['Nonprofit & Community', 'Religious Organization'],
        'mosque' => ['Nonprofit & Community', 'Religious Organization'],
        'temple' => ['Nonprofit & Community', 'Religious Organization'],
        'synagogue' => ['Nonprofit & Community', 'Religious Organization'],
        'religious organization' => ['Nonprofit & Community', 'Religious Organization'],
        'community center' => ['Nonprofit & Community', 'Community Center'],
        'advocacy' => ['Nonprofit & Community', 'Advocacy Group'],
        'foundation' => ['Nonprofit & Community', 'Foundation'],

        // Entertainment & Events
        'event planning' => ['Entertainment & Events', 'Event Planning'],
        'event planner' => ['Entertainment & Events', 'Event Planning'],
        'photographer' => ['Entertainment & Events', 'Photography & Videography'],
        'photography' => ['Entertainment & Events', 'Photography & Videography'],
        'videograph' => ['Entertainment & Events', 'Photography & Videography'],
        'dj' => ['Entertainment & Events', 'DJ & Entertainment'],
        'wedding' => ['Entertainment & Events', 'Wedding Services'],
        'party rental' => ['Entertainment & Events', 'Party Rentals'],
        'live music' => ['Entertainment & Events', 'Live Music Venue'],
        'music venue' => ['Entertainment & Events', 'Live Music Venue'],
        'nightclub' => ['Entertainment & Events', 'Live Music Venue'],

        // Agriculture
        'farm' => ['Agriculture', 'Farming'],
        'nursery' => ['Agriculture', 'Nursery & Garden Center'],
        'garden center' => ['Agriculture', 'Nursery & Garden Center'],
        'livestock' => ['Agriculture', 'Livestock'],
        'agritourism' => ['Agriculture', 'Agritourism'],
        'winery' => ['Agriculture', 'Winery & Vineyard'],
        'vineyard' => ['Agriculture', 'Winery & Vineyard'],

        // Transportation & Logistics
        'trucking' => ['Transportation & Logistics', 'Trucking'],
        'moving compan' => ['Transportation & Logistics', 'Moving Services'],
        'movers' => ['Transportation & Logistics', 'Moving Services'],
        'courier' => ['Transportation & Logistics', 'Courier & Delivery'],
        'delivery service' => ['Transportation & Logistics', 'Courier & Delivery'],
        'freight' => ['Transportation & Logistics', 'Freight Forwarding'],
        'taxi' => ['Transportation & Logistics', 'Taxi & Rideshare'],
        'rideshare' => ['Transportation & Logistics', 'Taxi & Rideshare'],
        'limo' => ['Transportation & Logistics', 'Taxi & Rideshare'],
        'warehous' => ['Transportation & Logistics', 'Warehousing'],

        // Financial Services
        'bank' => ['Financial Services', 'Banking'],
        'investment firm' => ['Financial Services', 'Investment Firm'],
        'credit union' => ['Financial Services', 'Credit Union'],
        'tax preparation' => ['Financial Services', 'Tax Preparation'],
        'tax service' => ['Financial Services', 'Tax Preparation'],
        'payday' => ['Financial Services', 'Payday Lending'],

        // Pet Services
        'pet groom' => ['Pet Services', 'Pet Grooming'],
        'pet boarding' => ['Pet Services', 'Pet Boarding'],
        'kennel' => ['Pet Services', 'Pet Boarding'],
        'dog walk' => ['Pet Services', 'Dog Walking'],
        'pet train' => ['Pet Services', 'Pet Training'],
        'pet store' => ['Pet Services', 'Pet Store'],
        'pet shop' => ['Pet Services', 'Pet Store'],
        'veterinar' => ['Pet Services', 'Veterinary'],
        'vet clinic' => ['Pet Services', 'Veterinary'],
        'animal hospital' => ['Pet Services', 'Veterinary'],
        'pet' => ['Pet Services', null],

        // Childcare & Family
        'daycare' => ['Childcare & Family', 'Daycare'],
        'day care' => ['Childcare & Family', 'Daycare'],
        'preschool' => ['Childcare & Family', 'Preschool'],
        'after school' => ['Childcare & Family', 'After-School Programs'],
        'babysit' => ['Childcare & Family', 'Babysitting Services'],
        'nanny' => ['Childcare & Family', 'Babysitting Services'],
        'summer camp' => ['Childcare & Family', 'Summer Camp'],
    ];

    /**
     * @param array<int, string> $categoryTitles the business's own Yelp
     *        category titles, in the order Yelp itself returned them —
     *        typically most-relevant first.
     * @return array{industry: ?string, subNiche: ?string}
     */
    public function classify(array $categoryTitles): array
    {
        $result = ['industry' => null, 'subNiche' => null];

        foreach ($categoryTitles as $title) {
            if (! is_string($title) || $title === '') {
                continue;
            }

            $match = $this->matchOne($title);

            if ($match === null) {
                continue;
            }

            if ($result['industry'] === null) {
                $result = $match;

                continue;
            }

            // Already have an industry from an earlier category but no
            // sub-niche yet — a LATER category that maps to the SAME
            // industry with a real sub-niche fills that gap in, rather
            // than being discarded just because it wasn't the first
            // category Yelp listed. A later category mapping to a
            // DIFFERENT industry is ignored — the first successfully
            // classified category's own industry wins, matching
            // firstCategoryTitle()'s own "most relevant first" ordering
            // assumption.
            if ($result['subNiche'] === null && $match['industry'] === $result['industry'] && $match['subNiche'] !== null) {
                $result['subNiche'] = $match['subNiche'];
            }
        }

        return $result;
    }

    /**
     * @return array{industry: string, subNiche: ?string}|null
     */
    private function matchOne(string $categoryTitle): ?array
    {
        $haystack = strtolower($categoryTitle);

        foreach (self::KEYWORD_MAP as $keyword => [$industry, $subNiche]) {
            if (str_contains($haystack, $keyword)) {
                return ['industry' => $industry, 'subNiche' => $subNiche];
            }
        }

        return null;
    }
}