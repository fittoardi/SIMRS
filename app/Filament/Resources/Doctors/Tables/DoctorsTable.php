<?php

namespace App\Filament\Resources\Doctors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'SIP: '.($record->sip_number ?? '-')),
                TextColumn::make('polyclinic.name')
                    ->label('Poliklinik')
                    ->badge()
                    ->color('teal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('specialization')
                    ->label('Spesialisasi')
                    ->searchable(),
                TextColumn::make('consultation_fee')
                    ->label('Tarif Konsultasi')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('schedules_count')
                    ->label('Jadwal Aktif')
                    ->counts('schedules')
                    ->badge()
                    ->color('info'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('polyclinic_id')
                    ->label('Poliklinik')
                    ->relationship('polyclinic', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
