<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationService $translationService
    ) {}

    public function store(CreateTranslationRequest $request): JsonResponse
    {
        $translation = $this->translationService->createTranslation($request->validated());

        return response()->json([
            'message' => 'Translation created successfully',
            'data' => $translation,
        ], 201);
    }

    public function update(UpdateTranslationRequest $request, int $id): JsonResponse
    {
        $translation = $this->translationService->updateTranslation($id, $request->validated());

        return response()->json([
            'message' => 'Translation updated successfully',
            'data' => $translation,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $translation = $this->translationService->getTranslation($id);

        return response()->json([
            'data' => $translation,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $filters = $request->only(['tag', 'key', 'content', 'locale']);
        $translations = $this->translationService->searchTranslations($filters);

        return response()->json([
            'data' => $translations,
            'count' => $translations->count(),
        ]);
    }

    public function export(string $locale): JsonResponse
    {
        $translations = $this->translationService->exportTranslations($locale);

        return response()->json($translations);
    }

    public function stats(): JsonResponse
    {
        $stats = app(\App\Repositories\TranslationRepository::class)->getTranslationStats();

        return response()->json([
            'data' => $stats,
        ]);
    }

    public function locales(): JsonResponse
    {
        $locales = $this->translationService->getSupportedLocales();

        return response()->json([
            'data' => $locales,
        ]);
    }
}
