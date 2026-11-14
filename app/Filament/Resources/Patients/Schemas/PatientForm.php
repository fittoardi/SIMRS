<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Models\Patient;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: IDENTITAS UTAMA PASIEN & REKAM MEDIS
                Section::make('Identitas Utama & Rekam Medis')
                    ->description('Data identitas resmi pasien sesuai KTP/KIA dan penomoran rekam medis')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('medical_record_number')
                                ->label('Nomor Rekam Medis (No. RM)')
                                ->default(fn () => Patient::generateMedicalRecordNumber())
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(30)
                                ->helperText('Nomor RM unik yang tertera pada kartu berobat pasien.'),

                            TextInput::make('nik')
                                ->label('Nomor Induk Kependudukan (NIK)')
                                ->placeholder('16 digit nomor KTP/KIA')
                                ->required()
                                ->length(16)
                                ->unique(ignoreRecord: true)
                                ->helperText('NIK 16 digit sesuai identitas kependudukan Dukcapil.'),
                        ]),

                        TextInput::make('name')
                            ->label('Nama Lengkap Pasien')
                            ->placeholder('Nama lengkap sesuai KTP/identitas')
                            ->required()
                            ->maxLength(255),

                        Grid::make(3)->schema([
                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                ])
                                ->required(),

                            DatePicker::make('birth_date')
                                ->label('Tanggal Lahir')
                                ->maxDate('today')
                                ->required(),

                            Select::make('blood_type')
                                ->label('Golongan Darah')
                                ->options([
                                    'A' => 'A',
                                    'B' => 'B',
                                    'AB' => 'AB',
                                    'O' => 'O',
                                    'tidak_tahu' => 'Tidak Tahu / Belum Cek',
                                ])
                                ->nullable(),
                        ]),
                    ]),

                // SECTION 2: KONTAK & DOMISILI
                Section::make('Kontak & Domisili')
                    ->description('Informasi kontak aktif untuk konfirmasi antrean dan alamat tinggal')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Textarea::make('address')
                            ->label('Alamat Domisili Lengkap')
                            ->placeholder('Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten')
                            ->required()
                            ->rows(3),
                    ]),

                // SECTION 3: JAMINAN KESEHATAN
                Section::make('Penjamin & Asuransi Kesehatan')
                    ->description('Informasi kepesertaan BPJS Kesehatan atau asuransi swasta')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('bpjs_number')
                                ->label('Nomor Kartu BPJS Kesehatan')
                                ->placeholder('13 digit nomor kartu BPJS')
                                ->maxLength(30),

                            TextInput::make('insurance_name')
                                ->label('Nama Asuransi Swasta (Jika Ada)')
                                ->placeholder('Contoh: Prudential / Mandiri Inhealth')
                                ->maxLength(100),

                            TextInput::make('insurance_number')
                                ->label('Nomor Polis / Kartu Asuransi')
                                ->placeholder('Nomor polis asuransi swasta')
                                ->maxLength(50),
                        ]),
                    ]),

                // SECTION 4: PERHATIAN ALERGI MEDIS
                Section::make('Perhatian Medis & Riwayat Alergi')
                    ->description('Penting: Data alergi akan menjadi peringatan darurat bagi dokter dan perawat')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->schema([
                        Textarea::make('allergies')
                            ->label('Riwayat Alergi (Obat, Makanan, Cuaca)')
                            ->placeholder('Contoh: Alergi Amoksisilin (syok/ruam), Alergi Parasetamol, Alergi Makanan Laut')
                            ->rows(2)
                            ->helperText('Kosongkan jika pasien tidak memiliki riwayat alergi yang diketahui.'),
                    ]),

                // SECTION 5: KONTAK DARURAT / PENANGGUNG JAWAB
                Section::make('Kontak Darurat (Penanggung Jawab)')
                    ->description('Keluarga terdekat yang dapat dihubungi dalam kondisi darurat')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('emergency_contact_name')
                                ->label('Nama Penanggung Jawab')
                                ->placeholder('Nama kerabat terdekat')
                                ->maxLength(100),

                            TextInput::make('emergency_contact_phone')
                                ->label('Nomor Telepon Darurat')
                                ->placeholder('08xxxxxxxxxx')
                                ->tel()
                                ->maxLength(30),

                            TextInput::make('emergency_contact_relation')
                                ->label('Hubungan dengan Pasien')
                                ->placeholder('Contoh: Suami / Istri / Orang Tua / Anak')
                                ->maxLength(50),
                        ]),
                    ]),
            ]);
    }
}
