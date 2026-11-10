<?php

namespace App\Filament\Resources\Polyclinics\Pages;

use App\Filament\Resources\Polyclinics\PolyclinicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPolyclinic extends EditRecord
{
    protected static string $resource = PolyclinicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
