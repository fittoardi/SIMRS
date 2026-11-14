<?php

namespace App\Filament\Resources\Patients\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('medical_record_number')
                    ->label('No. RM')
                    ->badge()
                    ->color('teal')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->tooltip('Klik untuk menyalin No. RM'),

                TextColumn::make('name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => 'NIK: '.($record->nik ?? '-')),

                TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'info',
                        'P' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('birth_date')
                    ->label('Tgl Lahir / Usia')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn ($record) => $record->birth_date ? $record->birth_date->age.' th' : '-'),

                TextColumn::make('phone')
                    ->label('Telepon / WA')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('blood_type')
                    ->label('Gol. Darah')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                TextColumn::make('allergies')
                    ->label('Riwayat Alergi')
                    ->badge()
                    ->color(fn ($state) => empty($state) ? 'gray' : 'danger')
                    ->formatStateUsing(fn ($state) => empty($state) ? 'Tidak Ada' : '⚠️ '.$state)
                    ->limit(24)
                    ->tooltip(fn ($record) => $record->allergies ?: 'Tidak ada riwayat alergi yang tercatat.'),

                TextColumn::make('visits_count')
                    ->label('Total Kunjungan')
                    ->counts('visits')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),

                SelectFilter::make('blood_type')
                    ->label('Golongan Darah')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'AB' => 'AB',
                        'O' => 'O',
                    ]),

                Filter::make('has_allergies')
                    ->label('Memiliki Riwayat Alergi')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('allergies')->where('allergies', '!=', '')),
            ])
            ->recordActions([
                Action::make('cetakKartu')
                    ->label('Cetak Kartu')
                    ->icon('heroicon-o-printer')
                    ->color('teal')
                    ->url(fn ($record) => route('patient.card', $record->id))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
