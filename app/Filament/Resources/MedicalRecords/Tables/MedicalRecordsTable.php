<?php

namespace App\Filament\Resources\MedicalRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MedicalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit.visit_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('visit.patient.medical_record_number')
                    ->label('No. RM')
                    ->badge()
                    ->color('teal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visit.patient.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'NIK: '.($record->visit?->patient?->nik ?? '-')),

                TextColumn::make('doctor.name')
                    ->label('Dokter Pemeriksa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visit.polyclinic.name')
                    ->label('Poliklinik')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('icd10_code')
                    ->label('ICD-10')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->description(fn ($record) => $record->assessment),

                TextColumn::make('blood_pressure')
                    ->label('Tanda Vital (TTV)')
                    ->formatStateUsing(fn ($record) => "TD: {$record->blood_pressure} | Nadi: {$record->heart_rate} | Suhu: {$record->temperature}°C")
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('polyclinic')
                    ->label('Poliklinik')
                    ->relationship('visit.polyclinic', 'name'),

                SelectFilter::make('doctor')
                    ->label('Dokter')
                    ->relationship('doctor', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
