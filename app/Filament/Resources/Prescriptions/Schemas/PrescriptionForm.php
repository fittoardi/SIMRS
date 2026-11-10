<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

use App\Enums\PrescriptionStatus;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\User;
use App\Models\Visit;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrescriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Resep & Kunjungan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('visit_id')
                                    ->label('Kunjungan Pasien')
                                    ->relationship('visit', 'id')
                                    ->getOptionLabelFromRecordUsing(fn (Visit $v) => "{$v->visit_number} — {$v->patient?->name} ({$v->polyclinic?->name})")
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $visit = Visit::find($state);
                                            if ($visit && $visit->doctor_id) {
                                                $set('doctor_id', $visit->doctor_id);
                                            }
                                        }
                                    }),

                                Select::make('doctor_id')
                                    ->label('Dokter Penulis Resep')
                                    ->relationship('doctor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('status')
                                    ->label('Status Resep')
                                    ->options(collect(PrescriptionStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(PrescriptionStatus::Pending->value)
                                    ->required(),

                                Select::make('pharmacist_id')
                                    ->label('Apoteker Penanggung Jawab')
                                    ->options(fn () => User::role(['pharmacist', 'super_admin'])->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                            ]),

                        Textarea::make('notes')
                            ->label('Catatan Apotek / Instruksi Khusus')
                            ->placeholder('Contoh: Pasien minta obat paten / generik, hindari sediaan kapsul')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Daftar Obat & Aturan Pakai')
                    ->description('Rincian obat yang diresepkan dan aturan pemakaian')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('medicine_id')
                                    ->label('Pilih Obat')
                                    ->options(fn () => Medicine::where('is_active', true)->get()->mapWithKeys(fn ($m) => [$m->id => "{$m->name} [Stok: {$m->stock} {$m->unit}] - Rp " . number_format($m->price, 0, ',', '.')]))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $med = Medicine::find($state);
                                        if ($med) {
                                            $set('medicine_name_snapshot', $med->name);
                                            $set('price_snapshot', $med->price);
                                            $qty = (int) ($get('qty') ?: 1);
                                            $set('subtotal', $med->price * $qty);
                                        }
                                    }),

                                TextInput::make('medicine_name_snapshot')
                                    ->label('Nama Obat')
                                    ->required(),

                                TextInput::make('qty')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = (float) ($get('price_snapshot') ?: 0);
                                        $set('subtotal', $price * (int) $state);
                                    }),

                                TextInput::make('dosage')
                                    ->label('Aturan Pakai (Signa)')
                                    ->placeholder('Contoh: 3x1 tablet sesudah makan')
                                    ->required(),

                                TextInput::make('price_snapshot')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('+ Tambah Obat ke Resep'),
                    ]),
            ]);
    }
}
