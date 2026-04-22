<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Database\Factories;

use Agenciafmd\Postal\Models\Postal;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PostalFactory extends Factory
{
    protected $model = Postal::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'is_active' => fake()->boolean(),
            'name' => ucfirst($name),
            'to' => fake()->safeEmail(),
            'to_name' => fake()->name(),
            'subject' => ucfirst(fake()->words(nb: 2, asText: true)),
            'cc' => [
                fake()->safeEmail(),
                fake()->safeEmail(),
            ],
            'bcc' => [
                fake()->safeEmail(),
            ],
            'slug' => str($name)->slug(),
        ];
    }
}
