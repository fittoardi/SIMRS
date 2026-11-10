<?php

namespace App\Filament\Resources\MedicalActions;

use App\Filament\Resources\MedicalActions\Pages\CreateMedicalAction;
use App\Filament\Resources\MedicalActions\Pages\EditMedicalAction;
use App\Filament\Resources\MedicalActions\Pages\ListMedicalActions;
use App\Filament\Resources\MedicalActions\Schemas\MedicalActionForm;
use App\Filament\Resources\MedicalActions\Tables\MedicalActionsTable;
use App\Models\MedicalAction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MedicalActionResource extends Resource
{
    protected static ?string $model = MedicalAction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Tindakan Medis';

    protected static ?string $modelLabel = 'Tindakan Medis';

    protected static ?string $pluralModelLabel = 'Katalog Tindakan Medis';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return MedicalActionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalActionsTable::configure($table);
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
            'index' => ListMedicalActions::route('/'),
            'create' => CreateMedicalAction::route('/create'),
            'edit' => EditMedicalAction::route('/{record}/edit'),
        ];
    }
}
