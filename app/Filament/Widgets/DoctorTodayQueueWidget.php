<?php

namespace App\Filament\Widgets;

use App\Enums\QueueStatus;
use App\Models\Doctor;
use App\Models\Visit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DoctorTodayQueueWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Antrean Pasien Saya Hari Ini';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('doctor');
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $doctor = $user ? Doctor::where('user_id', $user->id)->first() : null;
        $doctorId = $doctor?->id ?? 0;

        return $table
            ->query(
                Visit::query()
                    ->where('doctor_id', $doctorId)
                    ->whereDate('visit_date', today())
                    ->with(['patient', 'queue', 'polyclinic'])
                    ->whereHas('queue')
                    ->orderBy(
                        Visit::select('queue_number')
                            ->from('queues')
                            ->whereColumn('queues.visit_id', 'visits.id')
                            ->limit(1)
                    )
            )
            ->columns([
                TextColumn::make('queue.queue_code')
                    ->label('No. Antrean')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('patient.name')
                    ->label('Nama Pasien')
                    ->description(fn (Visit $record) => 'No. RM: '.($record->patient?->medical_record_number ?? '-')),
                TextColumn::make('polyclinic.name')
                    ->label('Poliklinik'),
                TextColumn::make('payment_type')
                    ->label('Penjamin')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'bpjs' => 'success',
                        'insurance' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('queue.status')
                    ->label('Status Antrean')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof QueueStatus ? $state->label() : (string) $state)
                    ->color(fn ($state) => $state instanceof QueueStatus ? $state->color() : 'gray'),
            ])
            ->emptyStateHeading('Belum ada pasien antrean hari ini')
            ->emptyStateDescription('Pasien yang terdaftar untuk jadwal praktik Anda akan muncul otomatis di sini.')
            ->paginated([5, 10]);
    }
}
