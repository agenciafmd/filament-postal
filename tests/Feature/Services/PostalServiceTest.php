<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Tests\Feature\Services;

use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Services\PostalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('lists every recipient once and in alphabetical order', function (): void {
    Postal::factory()->create(['to' => 'b@example.com', 'cc' => ['a@example.com'], 'bcc' => []]);
    Postal::factory()->create(['to' => 'a@example.com', 'cc' => [], 'bcc' => ['c@example.com']]);

    expect(PostalService::make()->emails()->all())->toBe([
        'a@example.com',
        'b@example.com',
        'c@example.com',
    ]);
});
