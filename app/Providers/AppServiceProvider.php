<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $appUrlPath = rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/');

        Livewire::setScriptRoute(function ($handle) use ($appUrlPath) {
            return Route::get($appUrlPath . '/livewire/livewire.js', $handle);
        });

        Livewire::setUpdateRoute(function ($handle) use ($appUrlPath) {
            return Route::post($appUrlPath . '/livewire/update', $handle);
        });
    }
}
