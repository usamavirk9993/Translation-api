<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    public function __construct(
        private TranslationRepository $translationRepository
    ) {}

    public function createTranslation(array $data): Translation
    {
        DB::beginTransaction();

        try {
            $translation = $this->translationRepository->create($data);

            if (isset($data['tags'])) {
                $this->syncTags($translation, $data['tags']);
            }

            $this->clearCache($translation->locale);
            DB::commit();

            return $translation->load('tags');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTranslation(int $id, array $data): Translation
    {
        DB::beginTransaction();

        try {
            $translation = $this->translationRepository->findById($id);
            $translation = $this->translationRepository->update($translation, $data);

            if (isset($data['tags'])) {
                $this->syncTags($translation, $data['tags']);
            }

            $this->clearCache($translation->locale);
            DB::commit();

            return $translation->load('tags');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getTranslation(int $id): Translation
    {
        return $this->translationRepository->findByIdWithTags($id);
    }

    public function searchTranslations(array $filters): Collection
    {
        return $this->translationRepository->search($filters);
    }

    public function exportTranslations(string $locale): array
    {
        $cacheKey = "translations_export_{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            return $this->translationRepository->getExportData($locale);
        });
    }

    public function getSupportedLocales(): array
    {
        return config('app.supported_locales', ['en', 'fr', 'es']);
    }

    public function addSupportedLocale(string $locale): void
    {
        $locales = $this->getSupportedLocales();

        if (! in_array($locale, $locales)) {
            $locales[] = $locale;
            config(['app.supported_locales' => $locales]);
        }
    }

    private function syncTags(Translation $translation, array $tagNames): void
    {
        $tagIds = collect($tagNames)->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        })->toArray();

        $translation->tags()->sync($tagIds);
    }

    private function clearCache(string $locale): void
    {
        Cache::forget("translations_export_{$locale}");
    }
}
