<?php

namespace App\Models;

use Database\Factories\SickLeaveCertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SickLeaveCertificate extends Model
{
    /** @use HasFactory<SickLeaveCertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'visit_id',
        'patient_id',
        'doctor_id',
        'certificate_number',
        'days_count',
        'start_date',
        'end_date',
        'diagnosis_note',
        'notes',
        'verification_token',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'days_count' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'issued_at' => 'datetime',
        ];
    }

    /**
     * Generate Nomor Surat Keterangan Sakit resmi (format: SK/YYYYMM/0001).
     */
    public static function generateCertificateNumber(): string
    {
        $prefix = 'SK/' . date('Ym') . '/';
        $latest = static::where('certificate_number', 'like', $prefix . '%')->latest('id')->first();
        $next = 1;

        if ($latest && preg_match('/\/(\d+)$/', $latest->certificate_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Token Verifikasi Publik (anti-pemalsuan).
     */
    public static function generateVerificationToken(): string
    {
        return 'SKT-' . strtoupper(bin2hex(random_bytes(16)));
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
