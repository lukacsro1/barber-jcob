<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->label('Barber')
                    ->relationship('barber', 'name', fn ($query) => $query->where('role', \App\Models\User::ROLE_BARBER))
                    ->required()
                    ->hidden(fn () => auth()->user()->isBarber())
                    ->default(fn () => auth()->id()),
                TextInput::make('customer_name')
                    ->required(),
                TextInput::make('service')
                    ->required(),
                DateTimePicker::make('start_at')
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('scheduled'),
            ]);

    }
}
