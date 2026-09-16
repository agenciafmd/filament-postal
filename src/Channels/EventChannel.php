<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Channels;

use Illuminate\Notifications\Notification;

final class EventChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        $lines = collect($notification->data['introLines']);

        $data['source'] = $notifiable->slug;

        $data = $lines
            ->map(fn ($line): string => mb_trim($line))
            ->map(fn ($line): string => str_replace('*', '', $line))
            ->filter(fn ($line): bool => str_contains($line, ':'))
            ->map(fn ($line): array => explode(':', $line, 2))
            ->mapWithKeys(function ($line): array {
                $key = str($line[0])
                    ->slug()
                    ->toString();
                $value = mb_trim($line[1]);

                return [
                    $key => $value,
                ];
            })
            ->filter()
            ->merge($data)
            ->toArray();

        $notification->toEvent($data);
    }
}
