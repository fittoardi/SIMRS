<?php

namespace App\Filament\Resources\MedicalRecords\Schemas;

use App\Models\Doctor;
use App\Models\MedicalAction;
use App\Models\User;
use App\Models\Visit;
use App\Support\Icd10Catalog;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class MedicalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: ANAMNESIS & IDENTITAS KUNJUNGAN
                Section::make('Anamnesis & Identitas Kunjungan')
                    ->description('Pilih kunjungan pasien dan catat keluhan utama pemeriksaan')
                    ->schema([
                        Select::make('visit_id')
                            ->label('Kunjungan Pasien')
                            ->relationship('visit', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Visit $v) => "{$v->visit_number} — {$v->patient?->name} (No. RM: {$v->patient?->medical_record_number}) [{$v->polyclinic?->name}]")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $visit = Visit::with(['patient', 'doctor'])->find($state);
                                    if ($visit) {
                                        if ($visit->doctor_id) {
                                            $set('doctor_id', $visit->doctor_id);
                                        }
                                    }
                                }
                            }),

                        // Peringatan Alergi Pasien (Warna Merah Tegas)
                        Placeholder::make('allergy_banner')
                            ->label('')
                            ->content(function (callable $get) {
                                $visitId = $get('visit_id');
                                if (! $visitId) {
                                    return null;
                                }

                                $visit = Visit::with('patient')->find($visitId);
                                $allergies = $visit?->patient?->allergies;

                                if (! empty($allergies)) {
                                    return new HtmlString('
                                        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border-2 border-rose-400 text-rose-800 dark:text-rose-200 text-sm flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-rose-200 dark:bg-rose-900 flex items-center justify-center shrink-0 text-rose-700 dark:text-rose-300 font-black">
                                                ⚠️
                                            </div>
                                            <div>
                                                <span class="font-extrabold uppercase tracking-wider block text-xs">PERHATIAN KLINIS — RIWAYAT ALERGI PASIEN:</span>
                                                <span class="font-bold text-base text-rose-900 dark:text-rose-100">' . e($allergies) . '</span>
                                                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5">Hindari peresepan obat atau zat yang memicu reaksi alergi pasien di atas.</p>
                                            </div>
                                        </div>
                                    ');
                                }

                                return new HtmlString('
                                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 text-emerald-800 dark:text-emerald-300 text-xs">
                                        ✓ Pasien tidak memiliki catatan riwayat alergi obat/makanan.
                                    </div>
                                ');
                            })
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Select::make('doctor_id')
                                    ->label('Dokter Pemeriksa')
                                    ->relationship('doctor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('nurse_id')
                                    ->label('Perawat Pendamping')
                                    ->options(fn () => User::role(['nurse', 'super_admin'])->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                            ]),

                        Textarea::make('subjective')
                            ->label('Anamnesis & Keluhan Utama (Subjective)')
                            ->placeholder('Keluhan utama, riwayat penyakit saat ini, durasi gejala, riwayat pengobatan sebelumnya')
                            ->rows(3)
                            ->columnSpanFull()
                            ->required(),
                    ]),

                // SECTION 2: PEMERIKSAAN FISIK & TANDA-TANDA VITAL (TTV)
                Section::make('Pemeriksaan Fisik & Tanda-Tanda Vital (TTV)')
                    ->description('Hasil observasi fisik dan pengukuran tanda vital pasien')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('blood_pressure')
                                    ->label('Tekanan Darah (TD)')
                                    ->placeholder('Contoh: 120/80')
                                    ->suffix('mmHg'),

                                TextInput::make('heart_rate')
                                    ->label('Denyut Nadi')
                                    ->numeric()
                                    ->placeholder('80')
                                    ->suffix('x/menit'),

                                TextInput::make('respiratory_rate')
                                    ->label('Laju Pernapasan')
                                    ->numeric()
                                    ->placeholder('20')
                                    ->suffix('x/menit'),

                                TextInput::make('temperature')
                                    ->label('Suhu Tubuh')
                                    ->numeric()
                                    ->placeholder('36.5')
                                    ->suffix('°C'),

                                TextInput::make('weight')
                                    ->label('Berat Badan')
                                    ->numeric()
                                    ->placeholder('65')
                                    ->suffix('kg'),

                                TextInput::make('height')
                                    ->label('Tinggi Badan')
                                    ->numeric()
                                    ->placeholder('170')
                                    ->suffix('cm'),
                            ]),

                        Textarea::make('objective')
                            ->label('Pemeriksaan Fisik Lengkap (Objective)')
                            ->placeholder('Kepala, leher, thoraks (jantung/paru), abdomen, ekstremitas, dan status lokalis')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                // SECTION 3: DIAGNOSA MEDIS (ICD-10 KEMENKES RI)
                Section::make('Diagnosa Medis (ICD-10 Kemenkes RI)')
                    ->description('Diagnosa kerja primer dan sekunder berdasarkan standar kodifikasi ICD-10')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('icd10_code')
                                    ->label('Kode ICD-10 Primer')
                                    ->options(Icd10Catalog::all())
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $catalog = Icd10Catalog::all();
                                        if (isset($catalog[$state])) {
                                            $desc = preg_replace('/^[A-Z0-9]+ - /', '', $catalog[$state]);
                                            $set('icd10_description', $desc);
                                            $set('assessment', $desc);
                                        }
                                    })
                                    ->required(),

                                TextInput::make('icd10_description')
                                    ->label('Deskripsi ICD-10 Primer')
                                    ->required(),
                            ]),

                        TextInput::make('assessment')
                            ->label('Diagnosa Kerja Utama (Assessment)')
                            ->placeholder('Diagnosa klinis dokter')
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('secondary_icd10_code')
                                    ->label('Kode ICD-10 Sekunder (Komorbiditas / Komplikasi)')
                                    ->placeholder('Contoh: I10'),

                                TextInput::make('secondary_diagnosis')
                                    ->label('Diagnosa Sekunder')
                                    ->placeholder('Diagnosa penyerta'),
                            ]),

                        Textarea::make('plan')
                            ->label('Rencana Penatalaksanaan & Edukasi (Plan)')
                            ->placeholder('Rencana terapi, instruksi diet, anjuran istirahat, rencana kontrol kembali')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->placeholder('Catatan khusus untuk perawat / rekan medis')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                // SECTION 4: TINDAKAN MEDIS
                Section::make('Tindakan Medis yang Diberikan')
                    ->description('Daftar tindakan medis rawat jalan yang dilakukan selama pemeriksaan')
                    ->schema([
                        Repeater::make('actions')
                            ->relationship('actions')
                            ->schema([
                                Select::make('medical_action_id')
                                    ->label('Tindakan Medis')
                                    ->options(fn () => MedicalAction::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $action = MedicalAction::find($state);
                                        if ($action) {
                                            $set('action_name_snapshot', $action->name);
                                            $set('price_snapshot', $action->price);
                                            $qty = (int) ($get('qty') ?: 1);
                                            $set('subtotal', $action->price * $qty);
                                        }
                                    }),

                                TextInput::make('action_name_snapshot')
                                    ->label('Nama Tindakan')
                                    ->required(),

                                TextInput::make('price_snapshot')
                                    ->label('Tarif (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                TextInput::make('qty')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = (float) ($get('price_snapshot') ?: 0);
                                        $set('subtotal', $price * (int) $state);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                TextInput::make('notes')
                                    ->label('Keterangan')
                                    ->placeholder('Misal: Jahitan 3 simpul')
                                    ->columnSpanFull(),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel('+ Tambah Tindakan Medis'),
                    ]),
            ]);
    }
}
