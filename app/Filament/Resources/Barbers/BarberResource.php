<?php

namespace App\Filament\Resources\Barbers;

use App\Filament\Resources\Barbers\Pages\CreateBarber;
use App\Filament\Resources\Barbers\Pages\EditBarber;
use App\Filament\Resources\Barbers\Pages\ListBarbers;
use App\Filament\Resources\Barbers\Schemas\BarberForm;
use App\Filament\Resources\Barbers\Tables\BarbersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class BarberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Barbers';

    protected static ?string $modelLabel = 'Barber';

    protected static ?string $pluralModelLabel = 'Barbers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', User::ROLE_BARBER);
    }


    public static function form(Schema $schema): Schema
    {
        return BarberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarbersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarbers::route('/'),
            'create' => CreateBarber::route('/create'),
            'edit' => EditBarber::route('/{record}/edit'),
        ];
    }
}
