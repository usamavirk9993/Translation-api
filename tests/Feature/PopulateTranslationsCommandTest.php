<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopulateTranslationsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_populates_database_with_translations(): void
    {
        $this->artisan('translations:populate', ['count' => 100])
            ->expectsOutput('Starting to populate database with 100 translation records...')
            ->expectsOutput('Chunk size: 1000')
            ->expectsOutput('Sample tags created/verified')
            ->assertExitCode(0);

        $this->assertDatabaseCount('translations', 100);
        $this->assertDatabaseCount('tags', 8); // mobile, desktop, web, admin, user, error, success, warning
    }

    public function test_command_creates_sample_tags(): void
    {
        $this->artisan('translations:populate', ['count' => 10]);

        $expectedTags = ['mobile', 'desktop', 'web', 'admin', 'user', 'error', 'success', 'warning'];

        foreach ($expectedTags as $tagName) {
            $this->assertDatabaseHas('tags', ['name' => $tagName]);
        }
    }

    public function test_command_with_custom_chunk_size(): void
    {
        $this->artisan('translations:populate', [
            'count' => 50,
            '--chunk' => 25,
        ])
            ->expectsOutput('Starting to populate database with 50 translation records...')
            ->expectsOutput('Chunk size: 25')
            ->assertExitCode(0);

        $this->assertDatabaseCount('translations', 50);
    }

    public function test_command_creates_translations_with_different_locales(): void
    {
        $this->artisan('translations:populate', ['count' => 30]);

        $translations = Translation::all();
        $locales = $translations->pluck('locale')->unique()->toArray();

        $this->assertContains('en', $locales);
        $this->assertContains('fr', $locales);
        $this->assertContains('es', $locales);
    }

    public function test_command_attaches_tags_to_translations(): void
    {
        $this->artisan('translations:populate', ['count' => 20]);

        $translations = Translation::with('tags')->get();

        // Check that at least some translations have tags
        $translationsWithTags = $translations->filter(function ($translation) {
            return $translation->tags->count() > 0;
        });

        $this->assertGreaterThan(0, $translationsWithTags->count());
    }

    public function test_command_generates_unique_keys(): void
    {
        $this->artisan('translations:populate', ['count' => 100]);

        $translations = Translation::all();
        $keys = $translations->pluck('key')->toArray();
        $uniqueKeys = array_unique($keys);

        $this->assertCount(100, $uniqueKeys);
        $this->assertCount(100, $keys);
    }

    public function test_command_performance_with_large_dataset(): void
    {
        $startTime = microtime(true);

        $this->artisan('translations:populate', ['count' => 1000, '--chunk' => 100]);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $this->assertDatabaseCount('translations', 1000);

        // Should complete in reasonable time (adjust threshold as needed)
        $this->assertLessThan(30000, $executionTime,
            "Command took {$executionTime}ms, should complete in reasonable time");
    }
}
