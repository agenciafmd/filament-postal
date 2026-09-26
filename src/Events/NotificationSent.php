<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class NotificationSent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @param  array<string, string>  $data  campos extraídos das linhas do e-mail, mais o `source`
     */
    public function __construct(public array $data) {}
}
