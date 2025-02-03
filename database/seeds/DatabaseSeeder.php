<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call(CreateAdminUserSeeder::class);
        $this->call(CreateAboutTableSeeder::class);
        $this->call(CreateSocialTableSeeder::class);
        $this->call(CreateSettingTableSeeder::class);
        $this->call(CreateSectionTableSeeder::class);
        $this->call(CreateLiveChatTableSeeder::class);
        $this->call(CreateTemplateTableSeeder::class);
        $this->call(CreateLanguageTableSeeder::class);
        $this->call(CreatePageSetupTableSeeder::class);
    }
}
