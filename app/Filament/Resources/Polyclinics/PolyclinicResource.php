<?php

namespace App\Filament\Resources\Polyclinics;

use App\Filament\Resources\Polyclinics\Pages\CreatePolyclinic;
use App\Filament\Resources\Polyclinics\Pages\EditPolyclinic;
use App\Filament\Resources\Polyclinics\Pages\ListPolyclinics;
use App\Filament\Resources\Polyclinics\Schemas\PolyclinicForm;
use App\Filament\Resources\Polyclinics\Tables\PolyclinicsTable;
use App\Models\Polyclinic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PolyclinicResource extends Resource
{
    protected static ?string $model = Polyclinic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Poliklinik';

    protected static ?string $modelLabel = 'Poliklinik';

    protected static ?string $pluralModelLabel = 'Daftar Poliklinik';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admission', 'nurse', 'doctor']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PolyclinicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PolyclinicsTable::configure($table);
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
            'index' => ListPolyclinics::route('/'),
            'create' => CreatePolyclinic::route('/create'),
            'edit' => EditPolyclinic::route('/{record}/edit'),
        ];
    }
}
