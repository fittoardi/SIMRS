<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalAction;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\Payment;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Queue;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoTransactionSeeder extends Seeder
{
    /**
     * Run the demo transactions database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        // 1. Ambil Akun Pasien Demo
        $patientUser = User::where('email', 'pasien@aisyahmedika.id')->first();
        if (! $patientUser) {
            return;
        }

        // Update No HP Pasien Demo ke nomor Fonnte aktif agar testing WhatsApp berjalan real
        $patientUser->update(['phone' => '085608543495']);

        // Pastikan Data Rekam Medis Pasien Utama
        $budi = Patient::updateOrCreate(
            ['medical_record_number' => 'RM-202611-0001'],
            [
                'user_id' => $patientUser->id,
                'nik' => '3201011234560001',
                'name' => 'Budi Santoso (DEMO)',
                'gender' => 'L',
                'birth_date' => '1988-06-15',
                'blood_type' => 'O',
                'allergies' => 'Penisilin, Seafood (Alergi Berat)',
                'phone' => '085608543495',
                'address' => 'Jl. Tebet Barat No. 12, Tebet, Jakarta Selatan',
            ]
        );

        PatientFamilyMember::firstOrCreate(
            ['user_id' => $patientUser->id, 'patient_id' => $budi->id],
            ['relationship' => 'diri_sendiri']
        );

        // Anggota Keluarga 1: Anak
        $anak = Patient::updateOrCreate(
            ['medical_record_number' => 'RM-202611-0002'],
            [
                'user_id' => null,
                'nik' => '3201015566770002',
                'name' => 'Siti Rahmawati (Anak - DEMO)',
                'gender' => 'P',
                'birth_date' => '2019-08-20',
                'blood_type' => 'O',
                'phone' => '085608543495',
                'address' => 'Jl. Tebet Barat No. 12, Tebet, Jakarta Selatan',
            ]
        );

        PatientFamilyMember::firstOrCreate(
            ['user_id' => $patientUser->id, 'patient_id' => $anak->id],
            ['relationship' => 'anak']
        );

        // Anggota Keluarga 2: Istri
        $istri = Patient::updateOrCreate(
            ['medical_record_number' => 'RM-202611-0003'],
            [
                'user_id' => null,
                'nik' => '3201016677880003',
                'name' => 'Ratna Dewi (Istri - DEMO)',
                'gender' => 'P',
                'birth_date' => '1992-03-10',
                'blood_type' => 'A',
                'phone' => '085608543495',
                'address' => 'Jl. Tebet Barat No. 12, Tebet, Jakarta Selatan',
            ]
        );

        PatientFamilyMember::firstOrCreate(
            ['user_id' => $patientUser->id, 'patient_id' => $istri->id],
            ['relationship' => 'istri']
        );

        // Dokter Demo
        $poliDalam = Polyclinic::where('slug', 'poliklinik-penyakit-dalam')->first() ?? Polyclinic::first();
        $poliUmum = Polyclinic::where('slug', 'poliklinik-umum')->first() ?? Polyclinic::first();
        $poliGigi = Polyclinic::where('slug', 'poliklinik-gigi-dan-mulut')->first() ?? Polyclinic::first();

        $dokterSpesialis = Doctor::where('polyclinic_id', $poliDalam?->id)->first() ?? Doctor::first();
        $dokterUmum = Doctor::where('polyclinic_id', $poliUmum?->id)->first() ?? Doctor::first();
        $dokterGigi = Doctor::where('polyclinic_id', $poliGigi?->id)->first() ?? Doctor::first();

        $scheduleDalam = DoctorSchedule::where('doctor_id', $dokterSpesialis?->id)->first();
        $scheduleUmum = DoctorSchedule::where('doctor_id', $dokterUmum?->id)->first();
        $scheduleGigi = DoctorSchedule::where('doctor_id', $dokterGigi?->id)->first();

        // =========================================================================
        // SCENARIO 1: Antrean Hari Ini Siap Dipanggil di Loket Panggilan
        // Pasien: Budi Santoso (Menunggu Panggilan Dokter Spesialis)
        // =========================================================================
        $visit1 = Visit::updateOrCreate(
            ['visit_number' => 'VIS-DEMO-001'],
            [
                'patient_id' => $budi->id,
                'doctor_id' => $dokterSpesialis?->id,
                'polyclinic_id' => $poliDalam?->id,
                'visit_date' => $today,
                'visit_type' => 'outpatient',
                'payment_type' => 'general',
                'status' => VisitStatus::Registered,
                'notes' => 'Kontrol berkala asam lambung dan evaluasi resep',
            ]
        );

        Queue::updateOrCreate(
            ['visit_id' => $visit1->id],
            [
                'doctor_schedule_id' => $scheduleDalam?->id,
                'queue_number' => 1,
                'queue_code' => 'DAL-'.date('ymd').'-001',
                'qr_token' => 'QR-DEMO-BUDI-001',
                'estimated_time' => '08:30',
                'status' => QueueStatus::Waiting,
            ]
        );

        // =========================================================================
        // SCENARIO 2: Pemeriksaan Sedang Berlangsung di Ruang Dokter (RME Aktif)
        // Pasien: Ratna Dewi (Sedang Dikonsultasi Dokter)
        // =========================================================================
        $visit2 = Visit::updateOrCreate(
            ['visit_number' => 'VIS-DEMO-002'],
            [
                'patient_id' => $istri->id,
                'doctor_id' => $dokterSpesialis?->id,
                'polyclinic_id' => $poliDalam?->id,
                'visit_date' => $today,
                'visit_type' => 'outpatient',
                'payment_type' => 'general',
                'status' => VisitStatus::InExamination,
                'notes' => 'Nyeri perut ulu hati sejak 2 hari lalu',
            ]
        );

        Queue::updateOrCreate(
            ['visit_id' => $visit2->id],
            [
                'doctor_schedule_id' => $scheduleDalam?->id,
                'queue_number' => 2,
                'queue_code' => 'DAL-'.date('ymd').'-002',
                'qr_token' => 'QR-DEMO-RATNA-002',
                'estimated_time' => '09:00',
                'status' => QueueStatus::Serving,
                'called_at' => now()->subMinutes(15),
                'served_at' => now()->subMinutes(10),
            ]
        );

        MedicalRecord::updateOrCreate(
            ['visit_id' => $visit2->id],
            [
                'doctor_id' => $dokterSpesialis?->id,
                'blood_pressure' => '118/78',
                'heart_rate' => 82,
                'temperature' => 36.7,
                'respiratory_rate' => 18,
                'weight' => 54,
                'height' => 158,
                'subjective' => 'Pasien mengeluh nyeri perih pada area ulu hati (epigastrium), mual di pagi hari, nafsu makan berkurang.',
                'objective' => 'Keadaan umum: Sedang, Compos Mentis. TD: 118/78 mmHg, Nadi: 82x/m, Suhu: 36.7 C, RR: 18x/m. Nyeri tekan epigastrium (+).',
                'assessment' => 'K30 - Dyspepsia / Gastritis Akut',
                'icd10_code' => 'K30',
                'icd10_description' => 'Dyspepsia',
                'plan' => 'Edukasi diet lambung (hindari pedas/kopi/asam), terapi obat supresi asam lambung & prokinetik, kontrol bila keluhan berlanjut 1 minggu.',
            ]
        );

        // =========================================================================
        // SCENARIO 3: Resep Obat Sedang Diracik di Farmasi (Status: Preparing)
        // Pasien: Siti Rahmawati (Anak)
        // Siap untuk Apoteker mencoba klik "Siap" -> Memicu WhatsApp Resep Selesai!
        // =========================================================================
        $visit3 = Visit::updateOrCreate(
            ['visit_number' => 'VIS-DEMO-003'],
            [
                'patient_id' => $anak->id,
                'doctor_id' => $dokterUmum?->id,
                'polyclinic_id' => $poliUmum?->id,
                'visit_date' => $today,
                'visit_type' => 'outpatient',
                'payment_type' => 'general',
                'status' => VisitStatus::InExamination,
                'notes' => 'Demam dan batuk berdahak sejak kemarin sore',
            ]
        );

        $prescription3 = Prescription::updateOrCreate(
            ['visit_id' => $visit3->id],
            [
                'doctor_id' => $dokterUmum?->id,
                'status' => PrescriptionStatus::Preparing,
                'notes' => 'Racikan sirup untuk anak berat badan 18 kg',
            ]
        );

        $sanmol = Medicine::where('sku', 'MED-006')->first() ?? Medicine::first();
        $ambroxol = Medicine::where('sku', 'MED-007')->first() ?? Medicine::skip(1)->first();

        if ($sanmol) {
            PrescriptionItem::updateOrCreate(
                ['prescription_id' => $prescription3->id, 'medicine_id' => $sanmol->id],
                [
                    'medicine_name_snapshot' => $sanmol->name,
                    'qty' => 1,
                    'dosage' => '3 x 1 sendok takar (5ml) bila demam',
                    'price_snapshot' => $sanmol->price,
                    'subtotal' => $sanmol->price * 1,
                ]
            );
        }

        if ($ambroxol) {
            PrescriptionItem::updateOrCreate(
                ['prescription_id' => $prescription3->id, 'medicine_id' => $ambroxol->id],
                [
                    'medicine_name_snapshot' => $ambroxol->name,
                    'qty' => 1,
                    'dosage' => '3 x 1/2 sendok takar (2.5ml) sesudah makan',
                    'price_snapshot' => $ambroxol->price,
                    'subtotal' => $ambroxol->price * 1,
                ]
            );
        }

        // =========================================================================
        // SCENARIO 4: Tagihan Belum Dibayar (Status: Unpaid)
        // Siap untuk Kasir mencoba Pelunasan Tunai/QRIS, atau Pasien bayar via Portal!
        // =========================================================================
        $visit4 = Visit::updateOrCreate(
            ['visit_number' => 'VIS-DEMO-004'],
            [
                'patient_id' => $budi->id,
                'doctor_id' => $dokterGigi?->id,
                'polyclinic_id' => $poliGigi?->id,
                'visit_date' => $today,
                'visit_type' => 'outpatient',
                'payment_type' => 'general',
                'status' => VisitStatus::Billing,
                'notes' => 'Pemeriksaan gigi rutin & pembersihan karang gigi (scaling)',
            ]
        );

        $invoice4 = Invoice::updateOrCreate(
            ['visit_id' => $visit4->id],
            [
                'patient_id' => $budi->id,
                'number' => 'INV-'.date('Ymd').'-0001',
                'subtotal' => 300000,
                'discount' => 0,
                'total' => 300000,
                'status' => PaymentStatus::Unpaid,
                'payment_status' => 'unpaid',
            ]
        );

        $actionScaling = MedicalAction::where('code', 'ACT-003')->first();
        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice4->id, 'item_type' => 'consultation'],
            [
                'name_snapshot' => 'Konsultasi Dokter Gigi (drg. Spesialis)',
                'qty' => 1,
                'price_snapshot' => 100000,
                'subtotal' => 100000,
            ]
        );

        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice4->id, 'item_type' => 'action'],
            [
                'name_snapshot' => $actionScaling?->name ?? 'Pembersihan Karang Gigi / Scaling',
                'qty' => 1,
                'price_snapshot' => 200000,
                'subtotal' => 200000,
            ]
        );

        // =========================================================================
        // SCENARIO 5: Kunjungan Kemarin Selesai & LUNAS (History & Kwitansi Cetak)
        // =========================================================================
        $visit5 = Visit::updateOrCreate(
            ['visit_number' => 'VIS-DEMO-005'],
            [
                'patient_id' => $budi->id,
                'doctor_id' => $dokterSpesialis?->id,
                'polyclinic_id' => $poliDalam?->id,
                'visit_date' => $yesterday,
                'visit_type' => 'outpatient',
                'payment_type' => 'general',
                'status' => VisitStatus::Completed,
                'notes' => 'Pemeriksaan laboratorium dan konsultasi spesialis',
            ]
        );

        $invoice5 = Invoice::updateOrCreate(
            ['visit_id' => $visit5->id],
            [
                'patient_id' => $budi->id,
                'number' => 'INV-'.date('Ymd', strtotime($yesterday)).'-0088',
                'subtotal' => 220000,
                'discount' => 0,
                'total' => 220000,
                'status' => PaymentStatus::Paid,
                'payment_status' => 'paid',
            ]
        );

        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice5->id, 'item_type' => 'consultation'],
            [
                'name_snapshot' => 'Konsultasi Spesialis Penyakit Dalam',
                'qty' => 1,
                'price_snapshot' => 150000,
                'subtotal' => 150000,
            ]
        );

        $omeprazole = Medicine::where('sku', 'MED-005')->first();
        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice5->id, 'item_type' => 'medicine'],
            [
                'name_snapshot' => $omeprazole?->name ?? 'Omeprazole 20mg Kapsul (2 Strip)',
                'qty' => 2,
                'price_snapshot' => 35000,
                'subtotal' => 70000,
            ]
        );

        Payment::updateOrCreate(
            ['invoice_id' => $invoice5->id],
            [
                'provider' => 'midtrans',
                'provider_ref' => 'MID-QRIS-DEMO-LUNAS-001',
                'method' => 'qris',
                'amount' => 220000,
                'status' => PaymentStatus::Paid,
                'paid_at' => Carbon::yesterday()->setHour(11)->setMinute(30),
                'raw_payload' => [
                    'channel' => 'QRIS',
                    'issuer' => 'BCA QRIS',
                    'acquirer' => 'Gopay',
                ],
            ]
        );
    }
}
