<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Enums\VisitStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Riwayat Kunjungan Pasien';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visit_number')
            ->columns([
                TextColumn::make('visit_number')
                    ->label('No. Kunjungan')
                    ->badge()
                    ->color('teal')
                    ->copyable()
                    ->sortable(),

                TextColumn::make('visit_date')
                    ->label('Tanggal Kunjungan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('polyclinic.name')
                    ->label('Poliklinik')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('doctor.name')
                    ->label('Dokter Spesialis')
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bpjs' => 'BPJS Kesehatan',
                        'insurance' => 'Asuransi',
                        default => 'Umum / Mandiri',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'bpjs' => 'success',
                        'insurance' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status Kunjungan')
                    ->badge()
                    ->formatStateUsing(fn (VisitStatus $state): string => $state->label())
                    ->color(fn (VisitStatus $state): string => $state->color()),
            ])
            ->defaultSort('visit_date', 'desc');
    }
}
