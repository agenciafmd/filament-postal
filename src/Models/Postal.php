<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Models;

use Agenciafmd\Postal\Database\Factories\PostalFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

#[UseFactory(PostalFactory::class)]
final class Postal extends Model implements AuditableContract
{
    use Auditable, HasFactory, Notifiable, Prunable, SoftDeletes;

    protected $table = 'postal';

    public function prunable(): Builder
    {
        return self::query()
            ->where('deleted_at', '<=', now()->subDays(30));
    }

    public function routeNotificationForMail(Notification $notification): array|string
    {
        return [
            $this->to => $this->to_name,
        ];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'cc' => 'array',
            'bcc' => 'array',
        ];
    }
}
