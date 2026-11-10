<?php

namespace App\Filament\Resources\Doctors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Jadwal Praktik';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('day_of_week')
                    ->label('Hari Praktik')
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ])
                    ->required(),
                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->seconds(false)
                    ->required(),
                TextInput::make('max_quota')
                    ->label('Kuota Pasien')
                    ->numeric()
                    ->default(20)
                    ->required(),
                TextInput::make('room_name')
                    ->label('Ruang Praktik')
                    ->placeholder('Misal: Poli 1')
                    ->maxLength(50),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                        default => '-',
                    })
                    ->badge()
                    ->color('teal')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Jam Praktik')
                    ->formatStateUsing(fn ($record) => substr($record->start_time, 0, 5).' - '.substr($record->end_time, 0, 5)),
                TextColumn::make('max_quota')
                    ->label('Kuota')
                    ->numeric()
                    ->badge(),
                TextColumn::make('room_name')
                    ->label('Ruangan')
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Sesi Praktik'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
