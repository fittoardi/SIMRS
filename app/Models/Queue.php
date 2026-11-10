<?php

namespace App\Models;

use App\Enums\QueueStatus;
use Database\Factories\QueueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    /** @use HasFactory<QueueFactory> */
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'doctor_schedule_id',
        'queue_number',
        'queue_code',
        'qr_token',
        'estimated_time',
        'status',
        'called_at',
        'served_at',
        'completed_at',
        'expires_at',
    ];

    public static function generateQrToken(): string
    {
        return 'TKT-' . strtoupper(bin2hex(random_bytes(12)));
    }

    protected function casts(): array
    {
        return [
            'queue_number' => 'integer',
            'status' => QueueStatus::class,
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function doctorSchedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class);
    }
}
