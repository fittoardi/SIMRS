<?php

namespace App\Filament\Resources\MedicalRecordAccessLogs\Pages;

use App\Filament\Resources\MedicalRecordAccessLogs\MedicalRecordAccessLogResource;
use Filament\Resources\Pages\ListRecords;

class ListMedicalRecordAccessLogs extends ListRecords
{
    protected static string $resource = MedicalRecordAccessLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

