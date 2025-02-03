<?php

use Illuminate\Database\Seeder;

class CreateSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sections')->delete();

        $sections = [
            ['title' => 'Latest Blog', 'slug' => 'blog', 'description' => ''],
            ['title' => 'Our Portfolios', 'slug' => 'portfolio', 'description' => ''],
            ['title' => 'Our Services', 'slug' => 'services', 'description' => ''],
            ['title' => 'Our Pricing', 'slug' => 'pricing', 'description' => ''],
            ['title' => 'Meet Our Teams', 'slug' => 'team', 'description' => ''],
            ['title' => 'Answer & Questions', 'slug' => 'faqs', 'description' => ''],
            ['title' => 'Our Partners', 'slug' => 'clients', 'description' => ''],
            ['title' => 'Our Clients Reviews', 'slug' => 'testimonials', 'description' => ''],
            ['title' => 'How We Make Work Successful', 'slug' => 'process', 'description' => ''],
            ['title' => 'Why Choose Us', 'slug' => 'why-us', 'description' => ''],
            ['title' => 'Newsletter - Get Updates & Latest News', 'slug' => 'subscribe', 'description' => 'Get in your inbox the latest News and Offers from'],
            ['title' => 'Get in Touch', 'slug' => 'contact', 'description' => ''],
            ['title' => 'Lets Talk About Your Idea', 'slug' => 'mail', 'description' => ''],
            ['title' => 'Get A Quote', 'slug' => 'get-quote', 'description' => 'Get a quote in just 30 minutes'],
            ['title' => 'Page Not Found', 'slug' => 'error', 'description' => 'The page you are Looking for was Moved, Removed, Renamed or Might Never Existed'],
            ['title' => 'Payment Feedback', 'slug' => 'payment', 'description' => ''],
        ];

        DB::table('sections')->insert($sections);
    }
}
