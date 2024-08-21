<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new LanguageSeeder)->run();
        (new AdminSeeder)->run();
        (new HarborSeeder)->run();
        (new LocationSeeder)->run();
        (new TagSeeder)->run();
        
        // FastBoat
        (new FastBoatSeeder)->run();
        (new FastBoatScheduleSeeder)->run();
        (new FastBoatScheduleVariantSeeder)->run();

        //PrivateBoat
        (new PrivateBoatSeeder)->run();
        (new PrivateBoatIncludeSeeder)->run();
        (new PrivateBoatSpecificationSeeder)->run();
        (new PrivateBoatTranslationSeeder)->run();
        (new PrivateBoatVariantSeeder)->run();
        (new PivotTagPrivateBoatSeeder)->run();

        //Activity
        (new ActivitySeeder)->run();
        (new ActivityIncludeSeeder)->run();
        (new ActivityExcludeSeeder)->run();
        (new ActivityTranslationSeeder)->run();
        (new ActivityVariantSeeder)->run();
        (new ActivityItinerarySeeder)->run();
        (new PivotTagActivitySeeder)->run();

        // (new ArticleCategorySeeder)->run();
        // (new ArticleSeeder)->run();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
