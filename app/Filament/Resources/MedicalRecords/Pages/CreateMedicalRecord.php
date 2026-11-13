<?php

namespace App\Filament\Resources\MedicalRecords\Pages;

use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use App\Services\MedicalAuditLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        if ($record->visit?->patient) {
            MedicalAuditLogger::log(
                patient: $record->visit->patient,
                action: 'create',
                medicalRecord: $record,
                notes: 'Pencatatan rekam medis baru di poliklinik'
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
