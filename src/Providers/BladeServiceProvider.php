<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Providers;

use Illuminate\Support\ServiceProvider;

final class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootViews();

        $this->bootPublish();
    }

    public function register(): void
    {
        //
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/mail', 'filament-postal');
    }

    private function bootPublish(): void
    {
        $this->publishes([
            __DIR__ . '/../../resources/mail' => base_path('resources/views/vendor/agenciafmd/filament-postal/mail'),
        ], 'filament-postal:mail');

        $this->publishes([
            __DIR__ . '/../../resources/images' => public_path('vendor/agenciafmd/filament-postal/images'),
        ], 'filament-postal:images');
    }
}
