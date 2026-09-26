<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Tests\Feature\Channels;

use Agenciafmd\Postal\Channels\EventChannel;
use Agenciafmd\Postal\Events\NotificationSent;
use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Notifications\SendNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('dispatches the fields of the mail lines with the postal slug as source', function (): void {
    Event::fake([NotificationSent::class]);
    $postal = Postal::factory()->make(['slug' => 'contato']);
    $notification = new SendNotification(['introLines' => [
        '**Nome:** Fulano ',
        'linha sem dois pontos',
        'Mensagem: a: b',
        'source: outro',
    ]]);

    new EventChannel()->send($postal, $notification);

    Event::assertDispatched(
        NotificationSent::class,
        static fn (NotificationSent $event): bool => $event->data === [
            'nome' => 'Fulano',
            'mensagem' => 'a: b',
            'source' => 'contato',
        ],
    );
});
