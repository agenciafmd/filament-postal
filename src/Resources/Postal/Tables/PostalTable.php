<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Resources\Postal\Tables;

use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Notifications\SendNotification;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
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
                Action::make('send')
                    ->translateLabel()
                    ->icon(Heroicon::PaperAirplane)
                    ->link()
                    ->action(function (Postal $record) {
                        try {
                            $record->notify(new SendNotification([
                                'greeting' => __('Hi :name!', ['name' => $record->to_name]),
                                'introLines' => [
                                    __('This is the test email sent by the website.'),
                                    '**' . ucfirst(__('Name')) . ":** {$record->name}",
                                    '**' . ucfirst(__('Subject')) . ":** {$record->subject}",
                                    '**' . ucfirst(__('To name')) . ":** {$record->to_name} ({$record->to})",
                                    '**' . ucfirst(__('Cc')) . ':** ' . (count($record->cc) ? implode(', ', $record->cc) : 'Nenhuma'),
                                    '**' . ucfirst(__('Bcc')) . ':** ' . (count($record->bcc) ? implode(', ', $record->bcc) : 'Nenhuma'),
                                ],
                                'actionText' => __('Visit the website'),
                                'actionUrl' => config('app.url'),
                                'outroLines' => [
                                    __('These are the lines below the button.'),
                                ],
                            ])
                            );

                            Notification::make()
                                ->title(__('Test send successfully.'))
                                ->success()
                                ->send();
                        } catch (Exception $exception) {
                            Notification::make()
                                ->title($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
