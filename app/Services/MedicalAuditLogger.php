<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class MedicalAuditLogger
{
    /**
     * Catat log audit akses data rekam medis pasien sesuai standar UU PDP No. 27/2022.
     */
    public static function log(
        Patient $patient,
        string $action,
        ?MedicalRecord $medicalRecord = null,
        ?User $user = null,
        ?string $notes = null
    ): MedicalRecordAccessLog {
        $user = $user ?? auth()->user();

        $roles = $user ? $user->getRoleNames()->toArray() : [];
        $userRole = ! empty($roles) ? implode(', ', $roles) : 'Guest / System';

        return MedicalRecordAccessLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Sistem / Anonim',
            'user_role' => $userRole,
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'medical_record_number' => $patient->medical_record_number,
            'medical_record_id' => $medicalRecord?->id,
            'action' => $action,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent() ?? 'CLI / Test',
            'notes' => $notes,
        ]);
    }
}

