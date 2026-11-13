<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
