<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Registered = 'registered';
    case InExamination = 'in_examination';
    case Pharmacy = 'pharmacy';
    case Billing = 'billing';
    case Completed = 'completed';
    case Discharged = 'discharged';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Terdaftar',
            self::InExamination => 'Dalam Pemeriksaan',
            self::Pharmacy => 'Menunggu Obat',
            self::Billing => 'Menunggu Pembayaran',
            self::Completed => 'Selesai',
            self::Discharged => 'Dipulangkan',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Registered => 'gray',
            self::InExamination => 'info',
            self::Pharmacy => 'warning',
            self::Billing => 'primary',
            self::Completed => 'success',
            self::Discharged => 'success',
            self::Cancelled => 'danger',
        };
    }
}
