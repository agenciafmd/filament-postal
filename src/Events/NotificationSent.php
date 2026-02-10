<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
