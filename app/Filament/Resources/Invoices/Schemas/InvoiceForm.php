<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Visit;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Tagihan & Kunjungan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('number')
                                    ->label('Nomor Tagihan')
                                    ->default(fn () => Invoice::generateInvoiceNumber())
                                    ->required()
                                    ->readOnly(),

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
                                            if ($visit && $visit->patient_id) {
                                                $set('patient_id', $visit->patient_id);
                                            }
                                        }
                                    }),

                                Select::make('patient_id')
                                    ->label('Nama Pasien')
                                    ->relationship('patient', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Patient $p) => "{$p->name} [RM: {$p->medical_record_number}]")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Status Tagihan')
                                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(PaymentStatus::Unpaid->value)
                                    ->required(),

                                TextInput::make('discount')
                                    ->label('Diskon / Potongan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $subtotal = (float) ($get('subtotal') ?: 0);
                                        $discount = (float) ($state ?: 0);
                                        $set('total', max(0, $subtotal - $discount));
                                    }),

                                TextInput::make('total')
                                    ->label('Total Akhir Tagihan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->readOnly(),
                            ]),

                        Textarea::make('notes')
                            ->label('Catatan Kasir / Keringanan')
                            ->placeholder('Contoh: Pasien mendapat diskon khusus pegawai / jaminan asuransi parsial')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Layanan & Tindakan (Billing Items)')
                    ->description('Daftar biaya konsultasi dokter, tindakan medis RME, resep obat, atau kamar rawat inap')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('item_type')
                                    ->label('Kategori')
                                    ->options([
                                        'registration' => 'Pendaftaran / Konsultasi',
                                        'action' => 'Tindakan Medis',
                                        'medicine' => 'Obat & Farmasi',
                                        'room' => 'Akomodasi Kamar',
                                        'other' => 'Lain-lain',
                                    ])
                                    ->default('action')
                                    ->required(),

                                TextInput::make('name_snapshot')
                                    ->label('Rincian Item')
                                    ->placeholder('Nama tindakan/obat/layanan')
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = (float) ($get('price_snapshot') ?: 0);
                                        $set('subtotal', $price * (int) $state);
                                    }),

                                TextInput::make('price_snapshot')
                                    ->label('Tarif Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $qty = (int) ($get('qty') ?: 1);
                                        $set('subtotal', (float) $state * $qty);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('+ Tambah Item Tagihan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

