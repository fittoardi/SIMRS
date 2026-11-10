<?php

namespace App\Filament\Resources\Medicines\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Obat')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->category.' • Satuan: '.$record->unit),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn ($record) => $record->stock <= $record->min_stock ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('min_stock')
                    ->label('Batas Min')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expired_at')
                    ->label('Kedaluwarsa')
                    ->date('d M Y')
                    ->color(fn ($record) => $record->expired_at && $record->expired_at->isPast() ? 'danger' : 'gray')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Kapsul',
                        'Sirup' => 'Sirup',
                        'Injeksi' => 'Injeksi',
                        'Salep' => 'Salep',
                        'Alkes' => 'Alkes',
                    ]),
                Filter::make('low_stock')
                    ->label('Stok Menipis / Kritis')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('stock', '<=', 'min_stock')),
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
