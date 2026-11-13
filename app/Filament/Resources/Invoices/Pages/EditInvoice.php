<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $subtotal = 0;
        if (! empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $subtotal += (float) ($item['subtotal'] ?? 0);
            }
        }
        $data['subtotal'] = $subtotal;
        $discount = (float) ($data['discount'] ?? 0);
        $data['total'] = max(0, $subtotal - $discount);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

