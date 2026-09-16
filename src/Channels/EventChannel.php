<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Channels;

use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Notifications\SendNotification;

final class EventChannel
{
    public function send(Postal $notifiable, SendNotification $notification): void
    {
        $data = collect($notification->data['introLines'] ?? [])
            ->map(fn (string $line): string => mb_trim($line))
            ->map(fn (string $line): string => str_replace('*', '', $line))
            ->filter(fn (string $line): bool => str_contains($line, ':'))
            ->map(fn (string $line): array => explode(':', $line, 2))
            ->mapWithKeys(function (array $line): array {
                $key = str($line[0])
                    ->slug()
                    ->toString();
                $value = mb_trim($line[1]);

                return [
                    $key => $value,
                ];
            })
            ->filter()
            ->put('source', $notifiable->slug)
            ->all();

        $notification->toEvent($data);
    }
}
