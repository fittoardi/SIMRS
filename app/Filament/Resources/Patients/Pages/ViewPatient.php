<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakKartu')
                ->label('Cetak Kartu Berobat')
                ->icon('heroicon-o-printer')
                ->color('teal')
                ->url(fn () => route('patient.card', $this->record->id))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
