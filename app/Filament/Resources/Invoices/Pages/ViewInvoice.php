<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakKwitansi')
                ->label('Cetak Kwitansi')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => url("/kasir/kwitansi/{$this->record->id}"))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}

