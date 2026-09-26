<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Resources\Postal;

use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Resources\Postal\Pages\CreatePostal;
use Agenciafmd\Postal\Resources\Postal\Pages\EditPostal;
use Agenciafmd\Postal\Resources\Postal\Pages\ListPostal;
use Agenciafmd\Postal\Resources\Postal\Schemas\PostalForm;
use Agenciafmd\Postal\Resources\Postal\Tables\PostalTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

final class PostalResource extends Resource
{
    protected static ?string $slug = 'postal';

    protected static ?string $model = Postal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Postal');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Forms');
    }

    /**
     * @return array<string, string>
     */
    public static function getExtraPermissions(): array
    {
        return [
            'send' => __('send'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        $navigationSort = config('filament-postal.navigation_sort');

        return is_int($navigationSort) ? $navigationSort : null;
    }

    public static function getNavigationGroup(): ?string
    {
        $navigationGroup = config('filament-postal.navigation_group');

        return is_string($navigationGroup) ? $navigationGroup : null;
    }

    public static function form(Schema $schema): Schema
    {
        return PostalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostalTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPostal::route('/'),
            'create' => CreatePostal::route('/create'),
            'edit' => EditPostal::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
