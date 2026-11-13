<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockMedicinesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Peringatan Stok Obat Menipis & Mendekati Kedaluwarsa';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'pharmacist']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Medicine::query()
                    ->where(function (Builder $query) {
                        $query->whereColumn('stock', '<=', 'min_stock')
                            ->orWhere('expired_at', '<=', now()->addDays(30));
                    })
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->badge(),
                TextColumn::make('name')
                    ->label('Nama Obat & Sediaan')
                    ->description(fn (Medicine $record) => $record->category.' • Satuan: '.$record->unit),
                TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->badge()
                    ->color(fn (Medicine $record) => $record->stock <= $record->min_stock ? 'danger' : 'warning'),
                TextColumn::make('min_stock')
                    ->label('Batas Minimum')
                    ->numeric(),
                TextColumn::make('expired_at')
                    ->label('Kedaluwarsa')
                    ->date('d M Y')
                    ->color(fn (Medicine $record) => $record->expired_at && $record->expired_at->isPast() ? 'danger' : 'warning'),
                TextColumn::make('status_warning')
                    ->label('Status Peringatan')
                    ->badge()
                    ->state(function (Medicine $record) {
                        if ($record->expired_at && $record->expired_at->isPast()) {
                            return 'KEDALUWARSA';
                        }
                        if ($record->stock <= 0) {
                            return 'HABIS TOTAL';
                        }
                        if ($record->stock <= $record->min_stock) {
                            return 'STOK MENIPIS';
                        }
                        if ($record->expired_at && $record->expired_at->lte(now()->addDays(30))) {
                            return 'DEKAT EXPIRED';
                        }

                        return 'PERHATIAN';
                    })
                    ->color(fn (string $state) => match ($state) {
                        'KEDALUWARSA', 'HABIS TOTAL' => 'danger',
                        'STOK MENIPIS' => 'warning',
                        default => 'info',
                    }),
            ])
            ->paginated([5, 10]);
    }
}
