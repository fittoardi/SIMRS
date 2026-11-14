<?php

namespace App\Http\Controllers;

use App\Models\SickLeaveCertificate;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SickLeaveCertificateController extends Controller
{
    /**
     * Tampilkan cetakan Surat Keterangan Sakit (Surat Izin Istirahat Medis) resmi.
     */
    public function show(Request $request, SickLeaveCertificate $certificate): View
    {
        $user = $request->user();

        // Otorisasi: Staf RS atau Pasien/Keluarga yang bersangkutan
        $isStaff = $user->hasAnyRole(['super_admin', 'doctor', 'nurse', 'admission', 'pharmacist', 'cashier']);
        $isOwner = false;

        if ($user->patient && $user->patient->id === $certificate->patient_id) {
            $isOwner = true;
        } elseif ($user->familyMembers()->where('patient_id', $certificate->patient_id)->exists()) {
            $isOwner = true;
        }

        if (! $isStaff && ! $isOwner) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat atau mencetak surat keterangan sakit ini.');
        }

        $certificate->loadMissing([
            'patient',
            'doctor.polyclinic',
            'visit.polyclinic',
            'medicalRecord',
        ]);

        $verificationUrl = route('sick-leave.verify', ['token' => $certificate->verification_token]);
        $qrSvg = QrCodeService::svg($verificationUrl, 140, 2);

        return view('medical.sick-leave', [
            'certificate' => $certificate,
            'verificationUrl' => $verificationUrl,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Halaman Publik Verifikasi Keaslian Surat Keterangan Sakit (Anti-Pemalsuan).
     */
    public function verify(string $token): View
    {
        $certificate = SickLeaveCertificate::with([
            'patient',
            'doctor.polyclinic',
            'visit.polyclinic',
        ])->where('verification_token', $token)->first();

        return view('medical.verify-sick-leave', [
            'certificate' => $certificate,
            'token' => $token,
        ]);
    }
}
