<?php

namespace App\Filament\Resources\MedicalActions\Pages;

use App\Filament\Resources\MedicalActions\MedicalActionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalAction extends EditRecord
{
    protected static string $resource = MedicalActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
