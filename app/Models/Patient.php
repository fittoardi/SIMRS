<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medical_record_number',
        'nik',
        'name',
        'gender',
        'birth_date',
        'blood_type',
        'allergies',
        'bpjs_number',
        'insurance_name',
        'insurance_number',
        'phone',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
    ];

    public static function generateMedicalRecordNumber(): string
    {
        $prefix = 'RM-' . date('Ym') . '-';
        $last = static::where('medical_record_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('medical_record_number');

        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $nextNumber = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
