<?php

namespace App\Filament\Resources\MedicalActions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MedicalActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Tindakan')
                    ->placeholder('Contoh: ACT-001')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(30),
                TextInput::make('name')
                    ->label('Nama Tindakan Medis')
                    ->placeholder('Contoh: Pemeriksaan Dokter Umum')
                    ->required()
                    ->maxLength(150),
                Select::make('polyclinic_id')
                    ->label('Poliklinik Terkait (Opsional)')
                    ->relationship('polyclinic', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('price')
                    ->label('Tarif Tindakan (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
