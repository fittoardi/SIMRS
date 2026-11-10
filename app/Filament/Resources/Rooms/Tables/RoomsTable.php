<?php

namespace App\Filament\Resources\Rooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Kamar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ward_name')
                    ->label('Bangsal')
                    ->searchable(),
                TextColumn::make('class_type')
                    ->label('Kelas')
                    ->badge()
                    ->color('teal')
                    ->sortable(),
                TextColumn::make('price_per_day')
                    ->label('Tarif / Hari')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('beds_count')
                    ->label('Jumlah Bed')
                    ->counts('beds')
                    ->badge()
                    ->color('info'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('class_type')
                    ->label('Kelas Kamar')
                    ->options([
                        'VVIP' => 'VVIP',
                        'VIP' => 'VIP',
                        'Kelas 1' => 'Kelas 1',
                        'Kelas 2' => 'Kelas 2',
                        'Kelas 3' => 'Kelas 3',
                        'ICU' => 'ICU',
                    ]),
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
