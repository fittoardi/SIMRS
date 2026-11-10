<?php

namespace App\Filament\Resources\Prescriptions\Tables;

use App\Enums\PrescriptionStatus;
use App\Enums\VisitStatus;
use App\Models\Medicine;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PrescriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Resep')
                    ->prefix('#RXP-')
                    ->sortable(),

                TextColumn::make('visit.visit_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('visit.patient.name')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'No. RM: ' . ($record->visit?->patient?->medical_record_number ?? '-')),

                TextColumn::make('doctor.name')
                    ->label('Dokter Penulis')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->visit?->polyclinic?->name),

                TextColumn::make('status')
                    ->label('Status Resep')
                    ->badge()
                    ->color(fn ($record) => $record->status->color())
                    ->formatStateUsing(fn ($record) => $record->status->label())
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Item Obat')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('pharmacist.name')
                    ->label('Apoteker')
                    ->placeholder('Belum diproses')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Resep')
                    ->options(collect(PrescriptionStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ])
            ->actions([
                // 1. Aksi Proses Resep
                Action::make('prosesResep')
                    ->label('Racik')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === PrescriptionStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => PrescriptionStatus::Preparing,
                            'pharmacist_id' => Auth::id(),
                        ]);

                        Notification::make()
                            ->info()
                            ->title('Resep Sedang Diracik')
                            ->body("Resep #RXP-{$record->id} sedang disiapkan oleh apoteker.")
                            ->send();
                    }),

                // 2. Aksi Obat Siap Diambil
                Action::make('siapDiambil')
                    ->label('Siap')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === PrescriptionStatus::Preparing)
                    ->action(function ($record) {
                        $record->update([
                            'status' => PrescriptionStatus::Ready,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Obat Siap Diambil')
                            ->body("Obat untuk pasien {$record->visit?->patient?->name} siap diambil di loket farmasi.")
                            ->send();
                    }),

                // 3. Aksi Serahkan Obat & Potong Stok
                Action::make('serahkanObat')
                    ->label('Serahkan Obat')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penyerahan Obat & Potong Stok')
                    ->modalDescription('Apakah Anda yakin ingin menyerahkan obat ini? Stok obat di farmasi akan otomatis terpotong di database.')
                    ->visible(fn ($record) => in_array($record->status, [PrescriptionStatus::Preparing, PrescriptionStatus::Ready]))
                    ->action(function ($record) {
                        // Periksa ketersediaan stok semua item terlebih dahulu
                        foreach ($record->items as $item) {
                            $medicine = Medicine::find($item->medicine_id);
                            if ($medicine && $medicine->stock < $item->qty) {
                                Notification::make()
                                    ->danger()
                                    ->title('Stok Obat Tidak Cukup!')
                                    ->body("Stok untuk obat {$medicine->name} tersisa {$medicine->stock}, dibutuhkan {$item->qty}.")
                                    ->send();

                                return;
                            }
                        }

                        // Potong stok obat
                        foreach ($record->items as $item) {
                            $medicine = Medicine::find($item->medicine_id);
                            if ($medicine) {
                                $medicine->decrement('stock', $item->qty);
                            }
                        }

                        $record->update([
                            'status' => PrescriptionStatus::HandedOver,
                            'pharmacist_id' => $record->pharmacist_id ?: Auth::id(),
                        ]);

                        // Update kunjungan ke status Selesai
                        $record->visit?->update(['status' => VisitStatus::Completed]);

                        Notification::make()
                            ->success()
                            ->title('Obat Telah Diserahkan!')
                            ->body("Resep #RXP-{$record->id} berhasil diserahkan. Stok obat otomatis terpotong di database.")
                            ->send();
                    }),

                // 4. Aksi Cetak Etiket Obat Termal
                Action::make('cetakEtiket')
                    ->label('Etiket')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => url("/farmasi/etiket/{$record->id}"))
                    ->openUrlInNewTab(),

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
