<?php

namespace App\Providers;

use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TranslationRepository::class, function ($app) {
            return new TranslationRepository;
        });

        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService($app->make(TranslationRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
