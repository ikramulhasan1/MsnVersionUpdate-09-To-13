<?php

use Illuminate\Database\Seeder;

class CreateTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')->delete();

        $templates = [
            ['title' => 'Your Quote Request Placed', 'slug' => 'quote-placed'],
            ['title' => 'Your Quote Request Estimated', 'slug' => 'quote-estimated'],
            ['title' => 'Your Quote Request Approved', 'slug' => 'quote-approved'],
            ['title' => 'Your Quote Request Rejected', 'slug' => 'quote-rejected'],


            ['title' => 'You Received a Payment Invoice', 'slug' => 'invoice-send'],
            ['title' => 'Your Payment Has Been Successfully Received', 'slug' => 'invoice-paid'],
            ['title' => 'You Have Cancelled a Payment Request', 'slug' => 'invoice-cancelled'],

            ['title' => 'This email is to notify that your subscription has been successful', 'slug' => 'subscription'],
        ];

        DB::table('email_templates')->insert($templates);
    }
}
