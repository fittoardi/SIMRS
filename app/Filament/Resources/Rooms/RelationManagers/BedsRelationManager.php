<?php

namespace App\Filament\Resources\Rooms\RelationManagers;

use App\Enums\BedStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BedsRelationManager extends RelationManager
{
    protected static string $relationship = 'beds';

    protected static ?string $title = 'Tempat Tidur (Bed)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bed_number')
                    ->label('Nomor / Kode Bed')
                    ->placeholder('Misal: BED-VVIP-01')
                    ->required()
                    ->maxLength(20),
                Select::make('status')
                    ->label('Status Ketersediaan')
                    ->options([
                        BedStatus::Available->value => 'Tersedia (Kosong)',
                        BedStatus::Occupied->value => 'Terisi Pasien',
                        BedStatus::Cleaning->value => 'Sedang Dibersihkan / Sterilisasi',
                        BedStatus::Maintenance->value => 'Perbaikan / Rusak',
                    ])
                    ->default(BedStatus::Available->value)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bed_number')
            ->columns([
                TextColumn::make('bed_number')
                    ->label('No. Bed')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (BedStatus $state): string => $state->color())
                    ->formatStateUsing(fn (BedStatus $state): string => $state->label()),
                TextColumn::make('currentVisit.patient.name')
                    ->label('Pasien yang Mengisi')
                    ->placeholder('Kosong')
                    ->description(fn ($record) => $record->currentVisit ? 'No. RM: '.($record->currentVisit->patient->medical_record_number ?? '-') : null),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Bed'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
