<?php

namespace App\Enums;

enum BedStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Cleaning = 'cleaning';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Occupied => 'Terisi',
            self::Cleaning => 'Dibersihkan',
            self::Maintenance => 'Perbaikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Occupied => 'danger',
            self::Cleaning => 'warning',
            self::Maintenance => 'gray',
        };
    }
}
