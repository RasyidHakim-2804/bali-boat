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
        // (new AdminSeeder)->run();
        // (new HarborSeeder)->run();
        // (new LocationSeeder)->run();
        // (new TagSeeder)->run();

        // Product Atribute
        (new ProductIncludeWithTranslationSeeder)->run();
        (new ProductExcludeWithTranslationSeeder)->run();
        (new ProductItineraryWithTranslationSeeder)->run();
        
        // FastBoat
        // (new FastBoatSeeder)->run();
        // (new FastBoatScheduleSeeder)->run();
        // (new FastBoatScheduleVariantSeeder)->run();

        // //PrivateBoat
        (new PrivateBoatSeeder)->run();
        (new PivotPrivateBoatIncludeSeeder)->run();
        (new PrivateBoatSpecificationSeeder)->run();
        (new PrivateBoatTranslationSeeder)->run();
        (new PrivateBoatVariantSeeder)->run();
        (new PivotTagPrivateBoatSeeder)->run();

        // //Activity
        (new ActivitySeeder)->run();
        (new ActivityVariantSeeder)->run();
        (new ActivityTranslationSeeder)->run();
        (new PivotTagActivitySeeder)->run();
        (new PivotActivityIncludeSeeder)->run();
        (new PivotActivityExcludeSeeder)->run();
        (new PivotActivityItinerarySeeder)->run();

        // (new ArticleCategorySeeder)->run();
        // (new ArticleSeeder)->run();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
