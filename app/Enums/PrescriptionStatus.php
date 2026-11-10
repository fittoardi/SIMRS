<?php

namespace App\Enums;

enum PrescriptionStatus: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case HandedOver = 'handed_over';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Diracik',
            self::Preparing => 'Sedang Diracik',
            self::Ready => 'Siap Diambil',
            self::HandedOver => 'Diserahkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Preparing => 'info',
            self::Ready => 'primary',
            self::HandedOver => 'success',
        };
    }
}
