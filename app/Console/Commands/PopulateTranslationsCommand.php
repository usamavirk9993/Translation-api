<?php

namespace App\Console\Commands;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopulateTranslationsCommand extends Command
{
    protected $signature = 'translations:populate {count=100000} {--chunk=1000}';

    protected $description = 'Populate database with sample translation records for scalability testing';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $chunkSize = (int) $this->option('chunk');

        $this->info("Starting to populate database with {$count} translation records...");
        $this->info("Chunk size: {$chunkSize}");

        // Create some sample tags if they don't exist
        $this->createSampleTags();

        $startTime = microtime(true);
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $locales = ['en', 'fr', 'es'];
        $tags = Tag::pluck('id')->toArray();

        for ($i = 0; $i < $count; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $count - $i);
            $this->createTranslationChunk($currentChunkSize, $locales, $tags);

            $progressBar->advance($currentChunkSize);
        }

        $progressBar->finish();
        $this->newLine();

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $this->info("Successfully created {$count} translation records in {$executionTime} seconds!");

        return Command::SUCCESS;
    }

    private function createSampleTags(): void
    {
        $tagNames = ['mobile', 'desktop', 'web', 'admin', 'user', 'error', 'success', 'warning'];

        foreach ($tagNames as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        $this->info('Sample tags created/verified');
    }

    private function createTranslationChunk(int $chunkSize, array $locales, array $tagIds): void
    {
        $translations = [];

        for ($i = 0; $i < $chunkSize; $i++) {
            $locale = $locales[array_rand($locales)];
            $key = $this->generateUniqueKey();

            $translations[] = [
                'locale' => $locale,
                'key' => $key,
                'content' => $this->generateContent($locale, $key),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Use chunk insert for better performance
        DB::table('translations')->insert($translations);

        // Attach random tags to translations (simplified approach)
        $this->attachRandomTags($chunkSize, $tagIds);
    }

    private function generateUniqueKey(): string
    {
        $prefixes = ['button', 'label', 'message', 'title', 'error', 'success', 'warning', 'info'];
        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = Str::random(8);

        return "{$prefix}.{$suffix}";
    }

    private function generateContent(string $locale, string $key): string
    {
        $contentMap = [
            'en' => [
                'button' => 'Click here',
                'label' => 'Enter your information',
                'message' => 'Operation completed successfully',
                'title' => 'Welcome to our application',
                'error' => 'An error occurred',
                'success' => 'Operation successful',
                'warning' => 'Please be careful',
                'info' => 'Important information',
            ],
            'fr' => [
                'button' => 'Cliquez ici',
                'label' => 'Entrez vos informations',
                'message' => 'Opération terminée avec succès',
                'title' => 'Bienvenue dans notre application',
                'error' => 'Une erreur s\'est produite',
                'success' => 'Opération réussie',
                'warning' => 'Soyez prudent',
                'info' => 'Informations importantes',
            ],
            'es' => [
                'button' => 'Haga clic aquí',
                'label' => 'Ingrese su información',
                'message' => 'Operación completada exitosamente',
                'title' => 'Bienvenido a nuestra aplicación',
                'error' => 'Ocurrió un error',
                'success' => 'Operación exitosa',
                'warning' => 'Por favor tenga cuidado',
                'info' => 'Información importante',
            ],
        ];

        $prefix = explode('.', $key)[0];
        $baseContent = $contentMap[$locale][$prefix] ?? 'Sample content';

        return $baseContent.' '.Str::random(5);
    }

    private function attachRandomTags(int $chunkSize, array $tagIds): void
    {
        if (empty($tagIds)) {
            return;
        }

        // Get the last inserted translations
        $lastTranslations = Translation::latest()->take($chunkSize)->get();

        foreach ($lastTranslations as $translation) {
            $randomTagCount = rand(1, 3);
            $randomTags = array_rand($tagIds, min($randomTagCount, count($tagIds)));

            if (is_array($randomTags)) {
                $selectedTagIds = array_map(fn ($index) => $tagIds[$index], $randomTags);
            } else {
                $selectedTagIds = [$tagIds[$randomTags]];
            }

            // Use syncWithoutDetaching to avoid duplicate constraint violations
            $translation->tags()->syncWithoutDetaching($selectedTagIds);
        }
    }
}
