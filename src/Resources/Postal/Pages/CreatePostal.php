<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Resources\Postal\Pages;

use Agenciafmd\Admix\Resources\Concerns\RedirectBack;
use Agenciafmd\Postal\Resources\Postal\PostalResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreatePostal extends CreateRecord
{
    use RedirectBack;

    #[Override]
    protected static string $resource = PostalResource::class;
}
