<?php

namespace App\Enums;

enum QueueStatus: string
{
    case Booked = 'booked';
    case Waiting = 'waiting';
    case Called = 'called';
    case Serving = 'serving';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Booked => 'Dibooking',
            self::Waiting => 'Menunggu',
            self::Called => 'Dipanggil',
            self::Serving => 'Sedang Dilayani',
            self::Completed => 'Selesai',
            self::Skipped => 'Dilewati',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Booked => 'gray',
            self::Waiting => 'warning',
            self::Called => 'info',
            self::Serving => 'primary',
            self::Completed => 'success',
            self::Skipped => 'danger',
            self::Cancelled => 'danger',
        };
    }
}
