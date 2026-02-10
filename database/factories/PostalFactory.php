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
        $name = fake()->sentence(3);

        return [
            'is_active' => fake()->boolean(),
            'name' => $name,
            'to' => fake()->safeEmail(),
            'to_name' => fake()->name(),
            'subject' => fake()->sentence(),
            'cc' => [
                fake()->safeEmail(),
                fake()->safeEmail(),
            ],
            'bcc' => [
                fake()->safeEmail(),
            ],
            'slug' => str()->slug($name),
        ];
    }
}
