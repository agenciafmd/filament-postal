<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Services;

use Agenciafmd\Postal\Models\Postal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class PostalService
{
    public static function make(): static
    {
        return app(self::class);
    }

    public function emails(): Collection
    {
        return $this->queryBuilder()
            ->select([
                'to',
                'cc',
                'bcc',
            ])
            ->get()
            ->map(fn ($item) => collect($item)->flatten())
            ->flatten()
            ->unique()
            ->sort()
            ->values();
    }

    private function queryBuilder(): Builder
    {
        return Postal::query();
    }
}
