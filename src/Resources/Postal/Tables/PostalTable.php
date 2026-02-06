<?php

namespace Agenciafmd\Postal\Resources\Postal\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class PostalTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('to')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('Identifier'))
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->translateLabel()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->translateLabel(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query->orderBy('is_active', 'desc')
                    ->orderBy('name');
            });
    }
}
