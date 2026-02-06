<?php

namespace Agenciafmd\Postal\Resources\Postal\Pages;

use Agenciafmd\Admix\Resources\Concerns\RedirectBack;
use Agenciafmd\Postal\Resources\Postal\PostalResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePostal extends CreateRecord
{
    use RedirectBack;

    protected static string $resource = PostalResource::class;
}
