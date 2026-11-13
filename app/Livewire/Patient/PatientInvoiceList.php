<?php

namespace App\Livewire\Patient;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientInvoiceList extends Component
{
    public string $statusFilter = 'all'; // all, unpaid, paid

    public function render()
    {
        $user = Auth::user();

        $patientIds = [];
        if ($user->patient) {
            $patientIds[] = $user->patient->id;
        }

        $familyPatientIds = $user->familyMembers()->pluck('patient_id')->toArray();
        $allPatientIds = array_unique(array_merge($patientIds, $familyPatientIds));

        $query = Invoice::with(['patient', 'visit.polyclinic', 'visit.doctor', 'payments'])
            ->whereIn('patient_id', $allPatientIds)
            ->latest();

        if ($this->statusFilter === 'unpaid') {
            $query->where('status', PaymentStatus::Unpaid);
        } elseif ($this->statusFilter === 'paid') {
            $query->where('status', PaymentStatus::Paid);
        }

        $invoices = $query->get();

        $unpaidCount = Invoice::whereIn('patient_id', $allPatientIds)
            ->where('status', PaymentStatus::Unpaid)
            ->count();

        $paidCount = Invoice::whereIn('patient_id', $allPatientIds)
            ->where('status', PaymentStatus::Paid)
            ->count();

        return view('livewire.patient.patient-invoice-list', [
            'invoices' => $invoices,
            'unpaidCount' => $unpaidCount,
            'paidCount' => $paidCount,
        ]);
    }
}
