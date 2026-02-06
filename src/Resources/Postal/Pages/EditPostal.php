<?php

namespace Agenciafmd\Postal\Resources\Postal\Pages;

use Agenciafmd\Admix\Resources\Concerns\RedirectBack;
use Agenciafmd\Postal\Resources\Postal\PostalResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPostal extends EditRecord
{
    use RedirectBack;

    protected static string $resource = PostalResource::class;

    protected $listeners = [
        'auditRestored',
    ];

    public function auditRestored(): void
    {
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
