<?php

namespace App\Filament\Resources\Medicines\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MedicineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('Kode SKU / Barcode')
                    ->placeholder('Contoh: MED-001')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                TextInput::make('name')
                    ->label('Nama Obat / Alkes')
                    ->placeholder('Contoh: Paracetamol 500mg')
                    ->required()
                    ->maxLength(150),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Kapsul',
                        'Sirup' => 'Sirup',
                        'Injeksi' => 'Injeksi',
                        'Salep' => 'Salep / Krim',
                        'Tetes' => 'Tetes (Mata / Telinga)',
                        'Alkes' => 'Alat Kesehatan',
                    ])
                    ->required(),
                Select::make('unit')
                    ->label('Satuan')
                    ->options([
                        'Strip' => 'Strip',
                        'Botol' => 'Botol',
                        'Tablet' => 'Tablet',
                        'Pcs' => 'Pcs',
                        'Ampul' => 'Ampul',
                        'Vial' => 'Vial',
                        'Tube' => 'Tube',
                    ])
                    ->required(),
                TextInput::make('price')
                    ->label('Harga Jual (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),
                TextInput::make('stock')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('min_stock')
                    ->label('Peringatan Stok Minimum')
                    ->numeric()
                    ->default(10)
                    ->required(),
                DatePicker::make('expired_at')
                    ->label('Tanggal Kedaluwarsa')
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
