<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\PaymentStatus;
use App\Services\BillingService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y H:i')
                    ->sortable(),

                TextColumn::make('patient.name')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'No. RM: ' . ($record->patient?->medical_record_number ?? '-')),

                TextColumn::make('visit.polyclinic.name')
                    ->label('Layanan / Poli')
                    ->description(fn ($record) => $record->visit?->doctor?->name ?? 'Dokter Jaga')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn ($record) => $record->status->color())
                    ->formatStateUsing(fn ($record) => $record->status->label())
                    ->sortable(),

                TextColumn::make('payments.method')
                    ->label('Metode Bayar')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => strtoupper((string) ($state ?: '-')))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ])
            ->actions([
                // 1. Aksi Pembayaran Kasir Tunai
                Action::make('bayarTunai')
                    ->label('Bayar Tunai')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === PaymentStatus::Unpaid)
                    ->form([
                        TextInput::make('total_display')
                            ->label('Total Tagihan')
                            ->default(fn ($record) => 'Rp ' . number_format((float) $record->total, 0, ',', '.'))
                            ->readOnly(),

                        TextInput::make('amount_tendered')
                            ->label('Nominal Uang Diterima')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn ($record) => (float) $record->total)
                            ->required()
                            ->helperText('Masukkan jumlah uang tunai yang diserahkan pasien ke kasir.'),

                        Textarea::make('notes')
                            ->label('Catatan Pembayaran')
                            ->placeholder('Opsional: Keterangan pembayaran kasir'),
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            $service = app(BillingService::class);
                            $result = $service->payWithCash(
                                $record,
                                (float) $data['amount_tendered'],
                                $data['notes'] ?? null,
                                Auth::id()
                            );

                            $changeFormatted = 'Rp ' . number_format($result['change'], 0, ',', '.');

                            Notification::make()
                                ->success()
                                ->title('Pembayaran Kasir Berhasil!')
                                ->body("Tagihan {$record->number} lunas. Kembalian: {$changeFormatted}")
                                ->persistent()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Pembayaran Gagal')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // 2. Aksi Pembayaran Digital / QRIS Dinamis
                Action::make('bayarDigital')
                    ->label('QRIS / VA')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === PaymentStatus::Unpaid)
                    ->form([
                        Select::make('method')
                            ->label('Kanal Pembayaran')
                            ->options([
                                'qris' => 'QRIS (Gopay / OVO / Dana / ShopeePay / BCA)',
                                'bca_va' => 'BCA Virtual Account',
                                'bni_va' => 'BNI Virtual Account',
                                'mandiri_va' => 'Mandiri Virtual Account',
                            ])
                            ->default('qris')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            $service = app(BillingService::class);
                            $service->payWithOnlineGateway($record, $data['method']);

                            Notification::make()
                                ->success()
                                ->title('Pembayaran Digital Berhasil!')
                                ->body("Tagihan {$record->number} telah terverifikasi lunas melalui {$data['method']}.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Memproses Pembayaran')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // 3. Cetak Kwitansi Kasir Termal
                Action::make('cetakKwitansi')
                    ->label('Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => url("/kasir/kwitansi/{$record->id}"))
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
