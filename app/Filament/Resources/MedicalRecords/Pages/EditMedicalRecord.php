<?php

namespace App\Filament\Resources\MedicalRecords\Pages;

use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use App\Services\MedicalAuditLogger;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalRecord extends EditRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;
        if ($record->visit?->patient) {
            MedicalAuditLogger::log(
                patient: $record->visit->patient,
                action: 'update',
                medicalRecord: $record,
                notes: 'Pembaruan data rekam medis pasien'
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
