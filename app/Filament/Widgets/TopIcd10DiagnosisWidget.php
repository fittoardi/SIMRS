<?php

namespace App\Filament\Widgets;

use App\Models\MedicalRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopIcd10DiagnosisWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '10 Penyakit / Diagnosis ICD-10 Terbanyak';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'doctor', 'nurse']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MedicalRecord::query()
                    ->select([
                        DB::raw('MIN(id) as id'),
                        'icd10_code',
                        'icd10_description',
                        DB::raw('COUNT(*) as total_cases'),
                    ])
                    ->whereNotNull('icd10_code')
                    ->where('icd10_code', '!=', '')
                    ->groupBy('icd10_code', 'icd10_description')
                    ->orderByDesc('total_cases')
            )
            ->columns([
                TextColumn::make('icd10_code')
                    ->label('Kode ICD-10')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('icd10_description')
                    ->label('Deskripsi Diagnosis Klinis')
                    ->wrap(),
                TextColumn::make('total_cases')
                    ->label('Jumlah Kasus')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
