<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample tags...');

        // Create sample tags
        $tagNames = ['mobile', 'desktop', 'web', 'admin', 'user', 'error', 'success', 'warning', 'info', 'help'];
        foreach ($tagNames as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        $this->command->info('Creating sample translations...');

        // Create sample translations for each locale
        $locales = ['en', 'fr', 'es'];
        $translationsPerLocale = 100; // Reduced for seeding, use command for large datasets

        foreach ($locales as $locale) {
            for ($i = 0; $i < $translationsPerLocale; $i++) {
                $translation = Translation::factory()->create([
                    'locale' => $locale,
                    'key' => "sample.{$locale}.{$i}",
                ]);

                // Attach random tags
                $randomTags = Tag::inRandomOrder()->take(rand(1, 3))->get();
                $translation->tags()->attach($randomTags);
            }
        }

        $this->command->info("Created {$translationsPerLocale} translations for each locale ({$locales[0]}, {$locales[1]}, {$locales[2]})");
        $this->command->info('For large datasets, use: php artisan translations:populate 100000');
    }
}
