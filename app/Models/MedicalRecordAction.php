<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'medical_action_id',
        'action_name_snapshot',
        'price_snapshot',
        'qty',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price_snapshot' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function medicalAction(): BelongsTo
    {
        return $this->belongsTo(MedicalAction::class);
    }
}
