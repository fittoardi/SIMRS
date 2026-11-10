<?php

namespace App\Models;

use Database\Factories\MedicalRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    /** @use HasFactory<MedicalRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'doctor_id',
        'nurse_id',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'weight',
        'height',
        'subjective',
        'objective',
        'assessment',
        'icd10_code',
        'icd10_description',
        'secondary_icd10_code',
        'secondary_diagnosis',
        'plan',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'heart_rate' => 'integer',
            'respiratory_rate' => 'integer',
            'temperature' => 'decimal:1',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function actions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MedicalRecordAction::class);
    }

    public function prescription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Prescription::class, 'visit_id', 'visit_id');
    }
}
