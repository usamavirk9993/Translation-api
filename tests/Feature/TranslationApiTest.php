<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_translation(): void
    {
        Sanctum::actingAs($this->user);

        $translationData = [
            'locale' => 'en',
            'key' => 'welcome.message',
            'content' => 'Welcome to our application',
            'tags' => ['web', 'user']
        ];

        $response = $this->postJson('/api/translations', $translationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'locale',
                    'key',
                    'content',
                    'tags'
                ]
            ]);

        $this->assertDatabaseHas('translations', [
            'locale' => 'en',
            'key' => 'welcome.message',
            'content' => 'Welcome to our application'
        ]);

        $this->assertDatabaseHas('tags', ['name' => 'web']);
        $this->assertDatabaseHas('tags', ['name' => 'user']);
    }

    public function test_user_can_update_translation(): void
    {
        Sanctum::actingAs($this->user);

        $translation = Translation::factory()->create();
        $tag = Tag::factory()->create(['name' => 'mobile']);

        $updateData = [
            'content' => 'Updated content',
            'tags' => ['mobile', 'web']
        ];

        $response = $this->putJson("/api/translations/{$translation->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Translation updated successfully',
                'data' => [
                    'content' => 'Updated content'
                ]
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'content' => 'Updated content'
        ]);
    }

    public function test_user_can_view_translation(): void
    {
        Sanctum::actingAs($this->user);

        $translation = Translation::factory()->create();
        $tag = Tag::factory()->create();
        $translation->tags()->attach($tag);

        $response = $this->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'locale',
                    'key',
                    'content',
                    'tags'
                ]
            ]);
    }

    public function test_user_can_search_translations(): void
    {
        Sanctum::actingAs($this->user);

        // Create test data
        $tag = Tag::factory()->create(['name' => 'mobile']);
        $translation1 = Translation::factory()->create([
            'key' => 'mobile.welcome',
            'content' => 'Welcome to mobile app'
        ]);
        $translation1->tags()->attach($tag);

        $translation2 = Translation::factory()->create([
            'key' => 'desktop.welcome',
            'content' => 'Welcome to desktop app'
        ]);

        // Search by tag
        $response = $this->getJson('/api/translations/search?tag=mobile');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', 'mobile.welcome');

        // Search by key
        $response = $this->getJson('/api/translations/search?key=mobile');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', 'mobile.welcome');

        // Search by content
        $response = $this->getJson('/api/translations/search?content=mobile');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_can_export_translations(): void
    {
        Sanctum::actingAs($this->user);

        // Create test translations
        Translation::factory()->count(5)->create(['locale' => 'en']);

        $response = $this->getJson('/api/translations/export/en');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertCount(5, $data);
    }

    public function test_user_can_view_translation_stats(): void
    {
        Sanctum::actingAs($this->user);

        // Create test data
        Translation::factory()->count(3)->create(['locale' => 'en']);
        Translation::factory()->count(2)->create(['locale' => 'fr']);
        Tag::factory()->count(5)->create();

        $response = $this->getJson('/api/translations/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_translations',
                    'translations_by_locale',
                    'total_tags'
                ]
            ])
            ->assertJson([
                'data' => [
                    'total_translations' => 5,
                    'total_tags' => 5
                ]
            ]);
    }

    public function test_user_can_view_supported_locales(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/translations/locales');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => ['en', 'fr', 'es']
            ]);
    }

    public function test_validation_errors_for_invalid_data(): void
    {
        Sanctum::actingAs($this->user);

        // Test invalid locale
        $response = $this->postJson('/api/translations', [
            'locale' => 'invalid',
            'key' => 'test.key',
            'content' => 'Test content'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);

        // Test missing required fields
        $response = $this->postJson('/api/translations', [
            'locale' => 'en'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key', 'content']);
    }

    public function test_unauthorized_access_returns_401(): void
    {
        $response = $this->getJson('/api/translations/1');

        $response->assertStatus(401);
    }

    public function test_export_performance_requirement(): void
    {
        Sanctum::actingAs($this->user);

        // Create many translations to test performance
        Translation::factory()->count(1000)->create(['locale' => 'en']);

        $startTime = microtime(true);
        
        $response = $this->getJson('/api/translations/export/en');
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        
        // Performance requirement: export should complete in under 500ms
        $this->assertLessThan(500, $executionTime, 
            "Export endpoint took {$executionTime}ms, should be under 500ms");
    }

    public function test_crud_operations_performance_requirement(): void
    {
        Sanctum::actingAs($this->user);

        $translationData = [
            'locale' => 'en',
            'key' => 'performance.test',
            'content' => 'Performance test content',
            'tags' => ['test']
        ];

        // Test create performance
        $startTime = microtime(true);
        $response = $this->postJson('/api/translations', $translationData);
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(201);
        $this->assertLessThan(200, $executionTime, 
            "Create endpoint took {$executionTime}ms, should be under 200ms");

        $translationId = $response->json('data.id');

        // Test read performance
        $startTime = microtime(true);
        $response = $this->getJson("/api/translations/{$translationId}");
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(200, $executionTime, 
            "Read endpoint took {$executionTime}ms, should be under 200ms");
    }
}
