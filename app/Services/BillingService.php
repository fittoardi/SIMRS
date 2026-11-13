<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\VisitStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BillingService
{
    /**
     * Buat invoice otomatis dari rincian layanan kunjungan (konsultasi, tindakan RME, resep obat, & kamar).
     */
    public function createInvoiceForVisit(Visit $visit, float $discount = 0, ?string $notes = null): Invoice
    {
        return DB::transaction(function () use ($visit, $discount, $notes): Invoice {
            $visit->loadMissing([
                'patient',
                'doctor',
                'polyclinic',
                'medicalRecord.actions',
                'prescription.items.medicine',
                'bed.room',
            ]);

            // Jika kunjungan sudah memiliki tagihan yang belum dibayar, gunakan tagihan tersebut
            $existingInvoice = $visit->invoices()
                ->where('status', PaymentStatus::Unpaid)
                ->first();

            if ($existingInvoice) {
                return $existingInvoice;
            }

            $itemsToCreate = [];
            $subtotal = 0.0;

            // 1. Biaya Konsultasi Dokter & Administrasi Poliklinik
            $consultationFee = (float) ($visit->doctor?->consultation_fee ?: 50000);
            $doctorName = $visit->doctor?->name ?? 'Dokter Poliklinik';
            $poliName = $visit->polyclinic?->name ?? 'Poliklinik';
            $itemsToCreate[] = [
                'item_type' => 'registration',
                'name_snapshot' => "Jasa Konsultasi {$doctorName} ({$poliName})",
                'qty' => 1,
                'price_snapshot' => $consultationFee,
                'subtotal' => $consultationFee,
            ];
            $subtotal += $consultationFee;

            // 2. Tindakan Medis dari Catatan RME (MedicalRecord)
            if ($visit->medicalRecord && $visit->medicalRecord->actions->isNotEmpty()) {
                foreach ($visit->medicalRecord->actions as $action) {
                    $actionSubtotal = (float) $action->subtotal;
                    $itemsToCreate[] = [
                        'item_type' => 'action',
                        'name_snapshot' => $action->action_name_snapshot,
                        'qty' => $action->qty,
                        'price_snapshot' => (float) $action->price_snapshot,
                        'subtotal' => $actionSubtotal,
                    ];
                    $subtotal += $actionSubtotal;
                }
            }

            // 3. Obat & Alkes dari Resep Farmasi (Prescription)
            if ($visit->prescription && $visit->prescription->items->isNotEmpty()) {
                foreach ($visit->prescription->items as $medItem) {
                    $medSubtotal = (float) $medItem->subtotal;
                    $itemsToCreate[] = [
                        'item_type' => 'medicine',
                        'name_snapshot' => "{$medItem->medicine_name_snapshot} ({$medItem->dosage})",
                        'qty' => $medItem->qty,
                        'price_snapshot' => (float) $medItem->price_snapshot,
                        'subtotal' => $medSubtotal,
                    ];
                    $subtotal += $medSubtotal;
                }
            }

            // 4. Sewa Kamar Rawat Inap (jika tipe kunjungan inpatient)
            if ($visit->visit_type === 'inpatient' && $visit->bed && $visit->bed->room) {
                $roomPrice = (float) $visit->bed->room->price_per_day;
                $itemsToCreate[] = [
                    'item_type' => 'room',
                    'name_snapshot' => "Akomodasi {$visit->bed->room->name} (Bed {$visit->bed->bed_number})",
                    'qty' => 1,
                    'price_snapshot' => $roomPrice,
                    'subtotal' => $roomPrice,
                ];
                $subtotal += $roomPrice;
            }

            $discountAmount = max(0, min($discount, $subtotal));
            $total = max(0, $subtotal - $discountAmount);

            $invoice = Invoice::create([
                'number' => Invoice::generateInvoiceNumber(),
                'visit_id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'total' => $total,
                'status' => PaymentStatus::Unpaid,
                'payment_status' => 'unpaid',
                'notes' => $notes,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $invoice->items()->create($itemData);
            }

            // Update status kunjungan ke tahap pembayaran
            if ($visit->status !== VisitStatus::Completed && $visit->status !== VisitStatus::Cancelled) {
                $visit->update(['status' => VisitStatus::Billing]);
            }

            return $invoice;
        });
    }

    /**
     * Proses pembayaran tunai di loket kasir dengan perhitungan uang diterima & kembalian.
     *
     * @return array{payment: Payment, change: float}
     */
    public function payWithCash(Invoice $invoice, float $amountTendered, ?string $notes = null, ?int $cashierUserId = null): array
    {
        return DB::transaction(function () use ($invoice, $amountTendered, $notes, $cashierUserId): array {
            $invoice->refresh();

            if ($invoice->status === PaymentStatus::Paid) {
                throw new InvalidArgumentException("Tagihan {$invoice->number} sudah berstatus LUNAS.");
            }

            if ($amountTendered < (float) $invoice->total) {
                $kurang = (float) $invoice->total - $amountTendered;
                throw new InvalidArgumentException('Nominal uang diterima kurang dari total tagihan (Kekurangan: Rp ' . number_format($kurang, 0, ',', '.') . ').');
            }

            $change = $amountTendered - (float) $invoice->total;

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'provider' => 'cash',
                'provider_ref' => 'CASH-' . date('YmdHis') . '-' . str_pad((string) $invoice->id, 4, '0', STR_PAD_LEFT),
                'method' => 'cash',
                'amount' => $invoice->total,
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
                'raw_payload' => [
                    'amount_tendered' => $amountTendered,
                    'change' => $change,
                    'cashier_user_id' => $cashierUserId,
                    'notes' => $notes,
                ],
            ]);

            $invoice->update([
                'status' => PaymentStatus::Paid,
                'payment_status' => 'paid',
            ]);

            // Selesaikan kunjungan pasien
            $invoice->visit?->update([
                'status' => VisitStatus::Completed,
            ]);

            return [
                'payment' => $payment,
                'change' => $change,
            ];
        });
    }

    /**
     * Proses pembayaran non-tunai / online gateway (QRIS, Virtual Account, E-Wallet).
     *
     * @param array<string, mixed> $extraPayload
     */
    public function payWithOnlineGateway(Invoice $invoice, string $method = 'qris', ?string $providerRef = null, array $extraPayload = []): Payment
    {
        return DB::transaction(function () use ($invoice, $method, $providerRef, $extraPayload): Payment {
            $invoice->refresh();

            if ($invoice->status === PaymentStatus::Paid) {
                throw new InvalidArgumentException("Tagihan {$invoice->number} sudah berstatus LUNAS.");
            }

            $ref = $providerRef ?: ('MID-' . strtoupper($method) . '-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3))));

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'provider' => 'midtrans',
                'provider_ref' => $ref,
                'method' => $method,
                'amount' => $invoice->total,
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
                'raw_payload' => array_merge([
                    'channel' => strtoupper($method),
                    'gateway' => 'Simulasi Payment Gateway RS Aisyah Medika',
                ], $extraPayload),
            ]);

            $invoice->update([
                'status' => PaymentStatus::Paid,
                'payment_status' => 'paid',
            ]);

            // Selesaikan kunjungan pasien
            $invoice->visit?->update([
                'status' => VisitStatus::Completed,
            ]);

            return $payment;
        });
    }
}

