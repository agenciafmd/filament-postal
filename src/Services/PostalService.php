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
        return resolve(self::class);
    }

    /**
     * Todos os e-mails de destino, cópia e cópia oculta, sem repetição.
     *
     * @return Collection<int, string>
     */
    public function emails(): Collection
    {
        return $this->queryBuilder()
            ->select([
                'to',
                'cc',
                'bcc',
            ])
            ->get()
            ->flatMap(static fn (Postal $postal): array => [
                $postal->to,
                ...$postal->cc ?? [],
                ...$postal->bcc ?? [],
            ])
            ->filter(static fn (mixed $email): bool => is_string($email))
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * @return Builder<Postal>
     */
    private function queryBuilder(): Builder
    {
        return Postal::query();
    }
}
