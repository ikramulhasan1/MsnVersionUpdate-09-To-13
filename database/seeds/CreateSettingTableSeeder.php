<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class CreateSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();
        
        $setting = Setting::create([

            'title'=>'HiTech - Multipurpose Business',
            'description'=>'HiTech multipurpose business website CMS (Content Management System), Which is fully customizable and 100% dynamic. By using our powerful Admin panel you can manage About Us (Inc. Mission & Vision) and Contact Page Details very easily. You can manage your website Sliders, Portfolios / Projects (Gallery), Services, Partners / Clients, Pricing Packages / Plan, Testimonials / Reviews, Team Members, FAQ’s (frequently asked questions) / Knowledge Base, Blog / News Posts, Work Process, Features / Why Choose Us, Counters, Messages / Emails, Subscribers List, Others Custom Pages, Social Profiles & More.',
            'keywords'=>'agency, business, business cms, company, consulting, corporate, creative, multipurpose, portfolio, pricing table, services, faq, marketing, management system, business website',
            'logo_path'=>'logo.png',
            'favicon_path'=>'favicon.png',
            'phone_one'=>'+880123456789',
            'email_one'=>'example@mail.com',
            'contact_address'=>'Mirpur, Dhaka',
            'contact_mail'=>'example@mail.com',
            'office_hours'=>' Monday to Friday 9:00am - 6:00pm',
            'footer_text'=>'2021 - HiTech - Multipurpose Business | Created By_ <a href="https://hitechparks.com/" target="_blank">Hi-Tech Parks</a>',
            'custom_css'=>' /** theme customize css **/ ',
            'status'=>'1'
            
        ]);
    }
}
