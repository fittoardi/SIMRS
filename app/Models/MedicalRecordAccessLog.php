<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordAccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'patient_id',
        'patient_name',
        'medical_record_number',
        'medical_record_id',
        'action',
        'ip_address',
        'user_agent',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}

