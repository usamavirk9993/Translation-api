<?php

namespace App\Repositories;

use App\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TranslationRepository
{
    public function create(array $data): Translation
    {
        return Translation::create($data);
    }

    public function update(Translation $translation, array $data): Translation
    {
        $translation->update($data);

        return $translation;
    }

    public function findById(int $id): Translation
    {
        return Translation::findOrFail($id);
    }

    public function findByIdWithTags(int $id): Translation
    {
        return Translation::with('tags')->findOrFail($id);
    }

    public function search(array $filters): Collection
    {
        $query = Translation::with('tags');

        if (isset($filters['tag'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['tag']}%");
            });
        }

        if (isset($filters['key'])) {
            $query->where('key', 'like', "%{$filters['key']}%");
        }

        if (isset($filters['content'])) {
            $query->where('content', 'like', "%{$filters['content']}%");
        }

        if (isset($filters['locale'])) {
            $query->where('locale', $filters['locale']);
        }

        return $query->get();
    }

    public function getExportData(string $locale): array
    {
        // Optimized query for export - only select needed fields
        return Translation::where('locale', $locale)
            ->select('key', 'content')
            ->get()
            ->pluck('content', 'key')
            ->toArray();
    }

    public function getTranslationsByLocale(string $locale, int $limit = 1000): Collection
    {
        return Translation::where('locale', $locale)
            ->with('tags')
            ->limit($limit)
            ->get();
    }

    public function getTranslationStats(): array
    {
        return [
            'total_translations' => Translation::count(),
            'translations_by_locale' => Translation::select('locale', DB::raw('count(*) as count'))
                ->groupBy('locale')
                ->pluck('count', 'locale')
                ->toArray(),
            'total_tags' => DB::table('tags')->count(),
        ];
    }

    public function bulkCreate(array $translations): void
    {
        // Use chunk insert for better performance
        collect($translations)->chunk(1000)->each(function ($chunk) {
            Translation::insert($chunk->toArray());
        });
    }
}
