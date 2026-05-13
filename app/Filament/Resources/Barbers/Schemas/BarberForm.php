<?php

namespace App\Filament\Resources\Barbers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BarberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->label('Barber Photo')
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('barbers')
                    ->imageEditor(),


                TextInput::make('name')
                    ->required(),
                TextInput::make('email')

                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('specialty'),
                TextInput::make('password')
                    ->password()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(fn ($state) => filled($state)),
                TextInput::make('role')
                    ->default(\App\Models\User::ROLE_BARBER)
                    ->hidden(),
            ]);


    }
}
