<?php

namespace App\Filament\Resources\MedicalRecordAccessLogs;

use App\Filament\Resources\MedicalRecordAccessLogs\Pages\ListMedicalRecordAccessLogs;
use App\Models\MedicalRecordAccessLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class MedicalRecordAccessLogResource extends Resource
{
    protected static ?string $model = MedicalRecordAccessLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Keamanan & Kepatuhan UU PDP';

    protected static ?string $navigationLabel = 'Audit Log Rekam Medis';

    protected static ?string $modelLabel = 'Log Akses Medis';

    protected static ?string $pluralModelLabel = 'Audit Log Akses Rekam Medis (UU PDP)';

    protected static ?int $navigationSort = 99;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Akses')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('user_name')
                    ->label('Staf Pengakses')
                    ->searchable()
                    ->description(fn (MedicalRecordAccessLog $record) => 'Role: '.($record->user_role ?? '-')),
                TextColumn::make('patient_name')
                    ->label('Pasien')
                    ->searchable()
                    ->description(fn (MedicalRecordAccessLog $record) => 'No. RM: '.($record->medical_record_number ?? '-')),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'create' => 'success',
                        'update' => 'warning',
                        'print', 'export' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('notes')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Filter Aksi')
                    ->options([
                        'view' => 'View (Melihat)',
                        'create' => 'Create (Membuat)',
                        'update' => 'Update (Mengubah)',
                        'print' => 'Print (Mencetak)',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicalRecordAccessLogs::route('/'),
        ];
    }
}
