<?php

namespace App\Filament\Resources\MedicalActions\Pages;

use App\Filament\Resources\MedicalActions\MedicalActionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicalActions extends ListRecords
{
    protected static string $resource = MedicalActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
