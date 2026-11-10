<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('polyclinic_id')
                    ->label('Poliklinik')
                    ->relationship('polyclinic', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Akun Login Dokter (Opsional)')
                    ->relationship('user', 'name', fn ($query) => $query->role('doctor'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('name')
                    ->label('Nama Lengkap & Gelar')
                    ->placeholder('Contoh: dr. Siti Aisyah, Sp.PD')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Slug URL Profil')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(180),
                TextInput::make('specialization')
                    ->label('Spesialisasi / Keahlian')
                    ->placeholder('Contoh: Spesialis Penyakit Dalam')
                    ->required()
                    ->maxLength(100),
                TextInput::make('sip_number')
                    ->label('Nomor SIP')
                    ->placeholder('Contoh: SIP/446/102/2024')
                    ->maxLength(50),
                TextInput::make('consultation_fee')
                    ->label('Tarif Jasa Konsultasi (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
                Textarea::make('bio')
                    ->label('Profil Singkat & Pengalaman')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
