<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacyEtiketController extends Controller
{
    /**
     * Tampilkan cetakan etiket obat termal untuk resep farmasi.
     */
    public function __invoke(Request $request, Prescription $prescription): View
    {
        // Pastikan hanya staf medis/farmasi/admin yang berhak mengakses cetak etiket
        if (! $request->user()->hasAnyRole(['super_admin', 'pharmacist', 'doctor', 'nurse', 'admission', 'cashier'])) {
            abort(403, 'Akses cetak etiket terbatas untuk tenaga medis dan farmasi RS Aisyah Medika.');
        }

        $prescription->load([
            'visit.patient',
            'visit.polyclinic',
            'doctor',
            'pharmacist',
            'items.medicine',
        ]);

        return view('pharmacy.etiket', [
            'prescription' => $prescription,
        ]);
    }
}
