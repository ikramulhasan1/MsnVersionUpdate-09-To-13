<?php

use Illuminate\Database\Seeder;

class CreatePageSetupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('page_setups')->delete();

        $page_setups = [
            ['title' => 'Home', 'slug' => 'home', 'meta_title' => 'Home'],
            ['title' => 'About Us', 'slug' => 'about-us', 'meta_title' => 'About Us'],
            ['title' => 'Services', 'slug' => 'services', 'meta_title' => 'Services'],
            ['title' => 'Portfolio', 'slug' => 'portfolio', 'meta_title' => 'Portfolio'],
            ['title' => 'Pricing', 'slug' => 'pricing', 'meta_title' => 'Pricing'],
            ['title' => 'Blog', 'slug' => 'blog', 'meta_title' => 'Blog'],
            ['title' => 'FAQs', 'slug' => 'faqs', 'meta_title' => 'FAQs'],
            ['title' => 'Contact Us', 'slug' => 'contact-us', 'meta_title' => 'Contact Us'],
            ['title' => 'Get A Quote', 'slug' => 'get-quote', 'meta_title' => 'Get A Quote'],
        ];

        DB::table('page_setups')->insert($page_setups);
    }
}
