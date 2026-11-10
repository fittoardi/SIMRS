<?php

namespace App\Models;

use App\Enums\BedStatus;
use Database\Factories\BedFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    /** @use HasFactory<BedFactory> */
    use HasFactory;

    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
        'current_visit_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => BedStatus::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function currentVisit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'current_visit_id');
    }
}
