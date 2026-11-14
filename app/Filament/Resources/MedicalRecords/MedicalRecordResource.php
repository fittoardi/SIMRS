<?php

namespace App\Filament\Resources\MedicalRecords;

use App\Filament\Resources\MedicalRecords\Pages\CreateMedicalRecord;
use App\Filament\Resources\MedicalRecords\Pages\EditMedicalRecord;
use App\Filament\Resources\MedicalRecords\Pages\ListMedicalRecords;
use App\Filament\Resources\MedicalRecords\Schemas\MedicalRecordForm;
use App\Filament\Resources\MedicalRecords\Tables\MedicalRecordsTable;
use App\Models\MedicalRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Pelayanan Pasien';

    protected static ?string $navigationLabel = 'Rekam Medis (RME)';

    protected static ?string $modelLabel = 'Rekam Medis Elektronik';

    protected static ?string $pluralModelLabel = 'Rekam Medis (RME)';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'doctor', 'nurse']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'doctor', 'nurse']) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'doctor']) ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return MedicalRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalRecordsTable::configure($table);
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
            'index' => ListMedicalRecords::route('/'),
            'create' => CreateMedicalRecord::route('/create'),
            'edit' => EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
