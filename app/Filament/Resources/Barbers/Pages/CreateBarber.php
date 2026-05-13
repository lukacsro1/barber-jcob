<?php

namespace App\Filament\Resources\Barbers\Pages;

use App\Filament\Resources\Barbers\BarberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarber extends CreateRecord
{
    protected static string $resource = BarberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = \App\Models\User::ROLE_BARBER;
        return $data;
    }
}

