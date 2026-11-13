<?php

namespace App\Livewire\Patient;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Services\BillingService;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientInvoiceDetail extends Component
{
    public int $invoiceId;

    public string $selectedMethod = 'qris'; // qris, bca_va, bni_va, mandiri_va

    public ?string $qrisSvg = null;

    public function mount(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
        $invoice = $this->authorizeAccess();

        // Generate dynamic QRIS SVG token if unpaid
        if ($invoice->status === PaymentStatus::Unpaid) {
            $qrisPayload = "00020101021226680016ID.CO.RSAM.WWW0118936009180000000000520454995303360540" . ((int) $invoice->total) . "5802ID5916RS AISYAH MEDIKA6007JAKARTA62070703A016304";
            $this->qrisSvg = QrCodeService::svg($qrisPayload, 200, 2);
        }
    }

    protected function authorizeAccess(): Invoice
    {
        $user = Auth::user();
        $invoice = Invoice::with(['patient', 'visit.doctor.polyclinic', 'visit.polyclinic', 'items', 'payments'])
            ->findOrFail($this->invoiceId);

        $isOwner = false;
        if ($user->patient && $user->patient->id === $invoice->patient_id) {
            $isOwner = true;
        } else {
            $familyPatientIds = $user->familyMembers()->pluck('patient_id')->toArray();
            if (in_array($invoice->patient_id, $familyPatientIds)) {
                $isOwner = true;
            }
        }

        if (! $isOwner && ! $user->hasAnyRole(['super_admin', 'cashier', 'doctor', 'nurse', 'admission', 'pharmacist'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rincian tagihan ini.');
        }

        return $invoice;
    }

    public function payNow(BillingService $billingService): void
    {
        $invoice = $this->authorizeAccess();

        if ($invoice->status === PaymentStatus::Paid) {
            session()->flash('error', 'Tagihan ini sudah berstatus LUNAS.');

            return;
        }

        try {
            $billingService->payWithOnlineGateway(
                $invoice,
                $this->selectedMethod,
                null,
                ['paid_by_patient_portal' => true]
            );

            session()->flash('success', "Pembayaran tagihan {$invoice->number} berhasil diverifikasi Lunas!");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $invoice = $this->authorizeAccess();

        return view('livewire.patient.patient-invoice-detail', [
            'invoice' => $invoice,
            'latestPayment' => $invoice->payments()->latest()->first(),
        ]);
    }
}
