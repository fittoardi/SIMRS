<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingKwitansiController extends Controller
{
    /**
     * Tampilkan cetakan struk kwitansi pembayaran kasir termal (58mm/80mm).
     */
    public function __invoke(Request $request, Invoice $invoice): View
    {
        $user = $request->user();

        // Otorisasi: Staf RS atau Pasien pemilik tagihan
        $isStaff = $user->hasAnyRole(['super_admin', 'cashier', 'doctor', 'nurse', 'admission', 'pharmacist']);
        $isOwner = false;

        if ($user->patient && $user->patient->id === $invoice->patient_id) {
            $isOwner = true;
        } else {
            $familyPatientIds = $user->familyMembers()->pluck('patient_id')->toArray();
            if (in_array($invoice->patient_id, $familyPatientIds)) {
                $isOwner = true;
            }
        }

        if (! $isStaff && ! $isOwner) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat kwitansi tagihan ini.');
        }

        $invoice->load([
            'patient',
            'visit.doctor.polyclinic',
            'visit.polyclinic',
            'items',
            'payments',
        ]);

        return view('billing.kwitansi', [
            'invoice' => $invoice,
            'latestPayment' => $invoice->payments()->latest()->first(),
        ]);
    }
}

