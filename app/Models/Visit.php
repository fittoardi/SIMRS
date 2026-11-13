<?php

namespace App\Models;

use App\Enums\VisitStatus;
use Database\Factories\VisitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    /** @use HasFactory<VisitFactory> */
    use HasFactory;

    protected $fillable = [
        'visit_number',
        'patient_id',
        'doctor_id',
        'polyclinic_id',
        'visit_date',
        'visit_type',
        'payment_type',
        'bpjs_card_number',
        'referral_letter_path',
        'insurance_name',
        'insurance_card_number',
        'status',
        'notes',
    ];

    public static function generateVisitNumber(): string
    {
        $prefix = 'VIS-' . date('Ymd') . '-';
        $last = static::where('visit_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('visit_number');

        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $next = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $prefix . $next;
    }

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'status' => VisitStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function polyclinic(): BelongsTo
    {
        return $this->belongsTo(Polyclinic::class);
    }

    public function queue(): HasOne
    {
        return $this->hasOne(Queue::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function bed(): HasOne
    {
        return $this->hasOne(Bed::class, 'current_visit_id');
    }
}
