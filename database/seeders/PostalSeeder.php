<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Database\Seeders;

use Agenciafmd\Postal\Models\Postal;
use Illuminate\Database\Seeder;

final class PostalSeeder extends Seeder
{
    public function run(): void
    {
        Postal::query()
            ->truncate();

        Postal::factory()
            ->count(10)
            ->create();
    }
}
