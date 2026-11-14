<?php

namespace App\Filament\Resources\MedicalRecords\Pages;

use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use App\Models\SickLeaveCertificate;
use App\Services\MedicalAuditLogger;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
            Action::make('suratSakit')
                ->label(fn () => $this->record->sickLeaveCertificate ? 'Kelola Surat Sakit' : 'Buat Surat Sakit')
                ->icon('heroicon-o-document-text')
                ->color(fn () => $this->record->sickLeaveCertificate ? 'success' : 'warning')
                ->modalHeading('Penerbitan Surat Keterangan Sakit Dokter')
                ->modalDescription('Terbitkan surat izin istirahat resmi dengan nomor registrasi dan QR Code verifikasi anti-pemalsuan.')
                ->modalSubmitActionLabel('Simpan & Terbitkan')
                ->form([
                    TextInput::make('days_count')
                        ->label('Lama Istirahat (Hari)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(30)
                        ->default(fn () => $this->record->sickLeaveCertificate?->days_count ?? 3)
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->default(fn () => $this->record->sickLeaveCertificate?->start_date ?? ($this->record->visit?->visit_date ?? now()))
                        ->required(),

                    DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->default(fn () => $this->record->sickLeaveCertificate?->end_date ?? now()->addDays(2))
                        ->required(),

                    Textarea::make('notes')
                        ->label('Catatan / Anjuran Dokter (Opsional)')
                        ->placeholder('Contoh: Pasien dianjurkan istirahat total dan perbanyak cairan.')
                        ->default(fn () => $this->record->sickLeaveCertificate?->notes)
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $record = $this->record;
                    $certificate = $record->sickLeaveCertificate;

                    if ($certificate) {
                        $certificate->update([
                            'days_count' => $data['days_count'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'notes' => $data['notes'] ?? null,
                        ]);
                    } else {
                        $certificate = SickLeaveCertificate::create([
                            'medical_record_id' => $record->id,
                            'visit_id' => $record->visit_id,
                            'patient_id' => $record->visit?->patient_id,
                            'doctor_id' => $record->doctor_id,
                            'certificate_number' => SickLeaveCertificate::generateCertificateNumber(),
                            'days_count' => $data['days_count'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'diagnosis_note' => $record->assessment ?? $record->icd10_code,
                            'notes' => $data['notes'] ?? null,
                            'verification_token' => SickLeaveCertificate::generateVerificationToken(),
                            'issued_at' => now(),
                        ]);
                    }

                    Notification::make()
                        ->success()
                        ->title('Surat Keterangan Sakit Berhasil Disimpan!')
                        ->body("Nomor Surat: {$certificate->certificate_number}")
                        ->send();
                }),

            Action::make('cetakSuratSakit')
                ->label('Cetak Surat Sakit')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->visible(fn () => (bool) $this->record->sickLeaveCertificate)
                ->url(fn () => route('sick-leave.print', $this->record->sickLeaveCertificate))
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
