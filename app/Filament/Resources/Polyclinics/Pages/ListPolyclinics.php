<?php

namespace App\Filament\Resources\Polyclinics\Pages;

use App\Filament\Resources\Polyclinics\PolyclinicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPolyclinics extends ListRecords
{
    protected static string $resource = PolyclinicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
