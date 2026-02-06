<?php

namespace Agenciafmd\Postal;

use Agenciafmd\Postal\Resources\Postal\PostalResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

final class PostalPlugin implements Plugin
{
    public static function make(): static
    {
        return app(self::class);
    }

    public function getId(): string
    {
        return 'postal';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                PostalResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
