<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Kamar')
                    ->placeholder('Contoh: R-VVIP-01')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                TextInput::make('name')
                    ->label('Nama Kamar')
                    ->placeholder('Contoh: Kamar Mawar 101')
                    ->required()
                    ->maxLength(100),
                TextInput::make('ward_name')
                    ->label('Nama Bangsal / Paviliun')
                    ->placeholder('Contoh: Bangsal Mawar (VVIP)')
                    ->required()
                    ->maxLength(100),
                Select::make('class_type')
                    ->label('Kelas Rawat Inap')
                    ->options([
                        'VVIP' => 'VVIP',
                        'VIP' => 'VIP',
                        'Kelas 1' => 'Kelas 1',
                        'Kelas 2' => 'Kelas 2',
                        'Kelas 3' => 'Kelas 3',
                        'ICU' => 'ICU (Intensive Care Unit)',
                        'NICU' => 'NICU / PICU',
                    ])
                    ->required(),
                TextInput::make('price_per_day')
                    ->label('Tarif Kamar per Hari (Rp)')
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
