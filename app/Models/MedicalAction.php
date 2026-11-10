<?php

namespace App\Models;

use Database\Factories\MedicalActionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalAction extends Model
{
    /** @use HasFactory<MedicalActionFactory> */
    use HasFactory;

    protected $fillable = [
        'polyclinic_id',
        'code',
        'name',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function polyclinic(): BelongsTo
    {
        return $this->belongsTo(Polyclinic::class);
    }
}
