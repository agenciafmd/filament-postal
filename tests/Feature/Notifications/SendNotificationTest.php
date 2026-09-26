<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Tests\Feature\Notifications;

use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Notifications\SendNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('builds the mail with the default greeting, subject and copies', function (): void {
    config()->set('app.name', 'FMD');
    $postal = Postal::factory()->make([
        'to_name' => 'Fulano',
        'subject' => 'Contato',
        'cc' => ['copia@example.com', ['invalid']],
        'bcc' => ['oculta@example.com'],
    ]);

    $mail = new SendNotification()->toMail($postal);

    expect($mail->greeting)->toBe(__('Hi :name!', ['name' => 'Fulano']))
        ->and($mail->subject)->toBe('FMD | Contato')
        ->and($mail->cc)->toBe([['copia@example.com', null]])
        ->and($mail->bcc)->toBe([['oculta@example.com', null]]);
});

it('uses the lines and the reply-to sent to the notification', function (): void {
    $postal = Postal::factory()->make(['cc' => [], 'bcc' => []]);

    $mail = new SendNotification(
        data: ['greeting' => null, 'introLines' => ['**Nome:** Fulano']],
        from: ['fulano@example.com' => 'Fulano'],
    )->toMail($postal);

    expect($mail->greeting)->toBeNull()
        ->and($mail->introLines)->toBe(['**Nome:** Fulano'])
        ->and($mail->replyTo)->toBe([['fulano@example.com', 'Fulano']]);
});
