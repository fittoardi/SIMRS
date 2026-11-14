<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientCardController extends Controller
{
    /**
     * Tampilkan cetakan Kartu Berobat Resmi Pasien RS Aisyah Medika (Ukuran ID Card Standar / Thermal).
     */
    public function __invoke(Request $request, Patient $patient): View
    {
        $user = $request->user();

        // Otorisasi: Staf RS atau Pasien/Keluarga yang sah
        $isStaff = $user->hasAnyRole(['super_admin', 'admission', 'doctor', 'nurse', 'pharmacist', 'cashier']);
        $isOwner = false;

        if ($user->patient && $user->patient->id === $patient->id) {
            $isOwner = true;
        } elseif ($user->familyMembers()->where('patient_id', $patient->id)->exists()) {
            $isOwner = true;
        }

        if (! $isStaff && ! $isOwner) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak kartu berobat pasien ini.');
        }

        $patient->loadMissing(['user', 'familyMembers']);

        // Generate QR Code No. RM untuk scanner kiosk / barcode reader loket
        $qrSvg = QrCodeService::svg($patient->medical_record_number, 130, 2);

        return view('patient.card', [
            'patient' => $patient,
            'qrSvg' => $qrSvg,
        ]);
    }
}
