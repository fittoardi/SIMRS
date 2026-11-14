<?php

namespace App\Filament\Resources\MedicalRecords\Tables;

use App\Models\SickLeaveCertificate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MedicalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit.visit_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('visit.patient.medical_record_number')
                    ->label('No. RM')
                    ->badge()
                    ->color('teal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visit.patient.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'NIK: '.($record->visit?->patient?->nik ?? '-')),

                TextColumn::make('doctor.name')
                    ->label('Dokter Pemeriksa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visit.polyclinic.name')
                    ->label('Poliklinik')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('icd10_code')
                    ->label('ICD-10')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->description(fn ($record) => $record->assessment),

                TextColumn::make('blood_pressure')
                    ->label('Tanda Vital (TTV)')
                    ->formatStateUsing(fn ($record) => "TD: {$record->blood_pressure} | Nadi: {$record->heart_rate} | Suhu: {$record->temperature}°C")
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('polyclinic')
                    ->label('Poliklinik')
                    ->relationship('visit.polyclinic', 'name'),

                SelectFilter::make('doctor')
                    ->label('Dokter')
                    ->relationship('doctor', 'name'),
            ])
            ->actions([
                Action::make('suratSakit')
                    ->label(fn ($record) => $record->sickLeaveCertificate ? 'Kelola Surat Sakit' : 'Buat Surat Sakit')
                    ->icon('heroicon-o-document-text')
                    ->color(fn ($record) => $record->sickLeaveCertificate ? 'success' : 'warning')
                    ->modalHeading(fn ($record) => 'Penerbitan Surat Keterangan Sakit — '.$record->visit?->patient?->name)
                    ->modalDescription('Terbitkan surat izin istirahat resmi dengan nomor registrasi dan QR Code verifikasi anti-pemalsuan.')
                    ->modalSubmitActionLabel('Simpan & Terbitkan')
                    ->form([
                        TextInput::make('days_count')
                            ->label('Lama Istirahat (Hari)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->default(fn ($record) => $record->sickLeaveCertificate?->days_count ?? 3)
                            ->required()
                            ->helperText('Jumlah hari istirahat yang dianjurkan oleh dokter.'),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->default(fn ($record) => $record->sickLeaveCertificate?->start_date ?? ($record->visit?->visit_date ?? now()))
                            ->required(),

                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->default(fn ($record) => $record->sickLeaveCertificate?->end_date ?? now()->addDays(2))
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan / Anjuran Dokter (Opsional)')
                            ->placeholder('Contoh: Pasien dianjurkan istirahat total, perbanyak cairan, dan kontrol ulang jika gejala berlanjut.')
                            ->default(fn ($record) => $record->sickLeaveCertificate?->notes)
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
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
                    ->label('Cetak Surat')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->visible(fn ($record) => (bool) $record->sickLeaveCertificate)
                    ->url(fn ($record) => route('sick-leave.print', $record->sickLeaveCertificate))
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
