<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Nette\Utils\Json;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     * @throws Exception
     */
    public function boot(): void
    {
        cache()->rememberForever(
            'translations_'.app()->getLocale(),
            fn () => Json::encode(
                Json::decode(file_get_contents(resource_path('lang/'.app()->getLocale().'.json')))
            )
        );
    }
}
