<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Resources\Postal\Schemas;

use Agenciafmd\Admix\Resources\Infolists\Components\DateTimeEntry;
use Agenciafmd\Postal\Services\PostalService;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

final class PostalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('General'))
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state, string $operation) {
                                if ($operation === Operation::Edit->value) {
                                    return;
                                }

                                if (($get('slug') ?? '') !== str($old)
                                    ->slug()
                                    ->toString()) {
                                    return;
                                }

                                $set('slug', str($state)
                                    ->slug()
                                    ->toString());
                            })
                            ->autofocus()
                            ->minLength(3)
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('slug')
                            ->label(__('Identifier'))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->disabledOn(operations: [
                                Operation::Edit,
                            ]),
                        TextInput::make('to')
                            ->translateLabel()
                            ->email()
                            ->required(),
                        TextInput::make('to_name')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('subject')
                            ->translateLabel()
                            ->required()
                            ->columnSpanFull(),
                        TagsInput::make('cc')
                            ->translateLabel()
                            ->placeholder(__('Add email'))
                            ->nestedRecursiveRules([
                                'email:rfc,dns',
                            ])
                            ->suggestions(fn (): array => PostalService::make()
                                ->emails()
                                ->toArray())
                            ->columnSpanFull(),
                        TagsInput::make('bcc')
                            ->translateLabel()
                            ->placeholder(__('Add email'))
                            ->nestedRecursiveRules([
                                'email:rfc,dns',
                            ])
                            ->suggestions(fn (): array => PostalService::make()
                                ->emails()
                                ->toArray())
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columns()
                    ->columnSpan(2),
                Section::make(__('Information'))
                    ->schema([
                        Toggle::make('is_active')
                            ->translateLabel()
                            ->default(true)
                            ->columnSpan(2),
                        DateTimeEntry::make('created_at'),
                        DateTimeEntry::make('updated_at'),
                    ])
                    ->collapsible()
                    ->columns(),
            ])
            ->columns(3);
    }
}
