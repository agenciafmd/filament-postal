<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Resources\Postal\Pages;

use Agenciafmd\Admix\Resources\Concerns\RedirectBack;
use Agenciafmd\Postal\Resources\Postal\PostalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\PageRegistration;

final class ListPostal extends ListRecords
{
    use RedirectBack;

    protected static string $resource = PostalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
