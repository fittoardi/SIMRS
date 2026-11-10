<?php

namespace Database\Seeders;

use App\Enums\BedStatus;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\MedicalAction;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\Polyclinic;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class HospitalMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Settings dasar RS Aisyah Medika
        Setting::set('hospital_info', [
            'name' => 'RS Aisyah Medika',
            'tagline' => 'Melayani dengan Hati, Mengabdi untuk Negeri',
            'phone' => '(021) 8765-4321',
            'whatsapp' => '081234567890',
            'emergency_call' => '119 / (021) 8765-4322',
            'email' => 'layanan@aisyahmedika.id',
            'address' => 'Jl. Kesehatan No. 45, Jakarta Selatan',
        ]);

        // 2. 4 Poliklinik CONTOH
        $polyclinicsData = [
            [
                'code' => 'POL-UMUM',
                'name' => 'Poliklinik Umum (CONTOH)',
                'slug' => 'poliklinik-umum',
                'description' => 'Pelayanan pemeriksaan medis dasar, pencegahan, dan rujukan awal oleh dokter umum berpengalaman.',
                'location' => 'Lantai 1, Sayap Timur Gedung A',
            ],
            [
                'code' => 'POL-DALAM',
                'name' => 'Poliklinik Penyakit Dalam (CONTOH)',
                'slug' => 'poliklinik-penyakit-dalam',
                'description' => 'Penanganan penyakit organ dalam pada dewasa dan lansia (diabetes, hipertensi, ginjal, saluran cerna).',
                'location' => 'Lantai 2, Sayap Barat Gedung A',
            ],
            [
                'code' => 'POL-GIGI',
                'name' => 'Poliklinik Gigi & Mulut (CONTOH)',
                'slug' => 'poliklinik-gigi-dan-mulut',
                'description' => 'Layanan perawatan gigi menyeluruh, konservasi gigi, pembersihan karang, dan pencabutan.',
                'location' => 'Lantai 1, Ruang 104 Gedung B',
            ],
            [
                'code' => 'POL-ANAK',
                'name' => 'Poliklinik Anak (CONTOH)',
                'slug' => 'poliklinik-anak',
                'description' => 'Pemeriksaan kesehatan bayi dan balita, imunisasi dasar & lanjutan, serta konsultasi tumbuh kembang.',
                'location' => 'Lantai 2, Ruang 205 Gedung B',
            ],
        ];

        $polyclinics = [];
        foreach ($polyclinicsData as $data) {
            $polyclinics[$data['code']] = Polyclinic::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['is_active' => true])
            );
        }

        // 3. 6 Dokter CONTOH + Jadwal Praktik
        $doctorUser = User::where('email', 'dokter@aisyahmedika.id')->first();

        $doctorsData = [
            [
                'user_id' => $doctorUser?->id,
                'polyclinic_id' => $polyclinics['POL-DALAM']->id,
                'name' => 'dr. Siti Aisyah, Sp.PD (CONTOH)',
                'slug' => 'dr-siti-aisyah-sppd',
                'sip_number' => 'SIP/446/102/2024',
                'specialization' => 'Spesialis Penyakit Dalam',
                'consultation_fee' => 150000,
                'bio' => 'Dokter spesialis penyakit dalam dengan pengalaman lebih dari 12 tahun menangani kasus metabolik & geriatri.',
                'schedules' => [
                    ['day' => 1, 'start' => '08:00:00', 'end' => '12:00:00', 'quota' => 20, 'room' => 'Poli Penyakit Dalam 1'],
                    ['day' => 3, 'start' => '08:00:00', 'end' => '12:00:00', 'quota' => 20, 'room' => 'Poli Penyakit Dalam 1'],
                    ['day' => 5, 'start' => '08:00:00', 'end' => '11:30:00', 'quota' => 15, 'room' => 'Poli Penyakit Dalam 1'],
                ],
            ],
            [
                'user_id' => null,
                'polyclinic_id' => $polyclinics['POL-DALAM']->id,
                'name' => 'dr. Bambang Irawan, Sp.PD (CONTOH)',
                'slug' => 'dr-bambang-irawan-sppd',
                'sip_number' => 'SIP/446/105/2024',
                'specialization' => 'Spesialis Penyakit Dalam',
                'consultation_fee' => 150000,
                'bio' => 'Ahli penyakit dalam fokus pada gastroenterologi dan hepatologi.',
                'schedules' => [
                    ['day' => 2, 'start' => '13:00:00', 'end' => '17:00:00', 'quota' => 15, 'room' => 'Poli Penyakit Dalam 2'],
                    ['day' => 4, 'start' => '13:00:00', 'end' => '17:00:00', 'quota' => 15, 'room' => 'Poli Penyakit Dalam 2'],
                ],
            ],
            [
                'user_id' => null,
                'polyclinic_id' => $polyclinics['POL-UMUM']->id,
                'name' => 'dr. Ahmad Fauzi (CONTOH)',
                'slug' => 'dr-ahmad-fauzi',
                'sip_number' => 'SIP/446/201/2023',
                'specialization' => 'Dokter Umum',
                'consultation_fee' => 50000,
                'bio' => 'Dokter umum ramah dan komunikatif, aktif mengedukasi pola hidup sehat.',
                'schedules' => [
                    ['day' => 1, 'start' => '08:00:00', 'end' => '14:00:00', 'quota' => 30, 'room' => 'Poli Umum 1'],
                    ['day' => 2, 'start' => '08:00:00', 'end' => '14:00:00', 'quota' => 30, 'room' => 'Poli Umum 1'],
                    ['day' => 3, 'start' => '08:00:00', 'end' => '14:00:00', 'quota' => 30, 'room' => 'Poli Umum 1'],
                    ['day' => 4, 'start' => '08:00:00', 'end' => '14:00:00', 'quota' => 30, 'room' => 'Poli Umum 1'],
                    ['day' => 5, 'start' => '08:00:00', 'end' => '14:00:00', 'quota' => 30, 'room' => 'Poli Umum 1'],
                    ['day' => 6, 'start' => '08:00:00', 'end' => '12:00:00', 'quota' => 20, 'room' => 'Poli Umum 1'],
                ],
            ],
            [
                'user_id' => null,
                'polyclinic_id' => $polyclinics['POL-UMUM']->id,
                'name' => 'dr. Nurul Hidayati (CONTOH)',
                'slug' => 'dr-nurul-hidayati',
                'sip_number' => 'SIP/446/204/2023',
                'specialization' => 'Dokter Umum',
                'consultation_fee' => 50000,
                'bio' => 'Dokter umum dengan ketelitian tinggi dalam diagnosis dini dan triage pasien.',
                'schedules' => [
                    ['day' => 1, 'start' => '14:00:00', 'end' => '20:00:00', 'quota' => 25, 'room' => 'Poli Umum 2'],
                    ['day' => 2, 'start' => '14:00:00', 'end' => '20:00:00', 'quota' => 25, 'room' => 'Poli Umum 2'],
                    ['day' => 3, 'start' => '14:00:00', 'end' => '20:00:00', 'quota' => 25, 'room' => 'Poli Umum 2'],
                    ['day' => 4, 'start' => '14:00:00', 'end' => '20:00:00', 'quota' => 25, 'room' => 'Poli Umum 2'],
                    ['day' => 5, 'start' => '14:00:00', 'end' => '20:00:00', 'quota' => 25, 'room' => 'Poli Umum 2'],
                ],
            ],
            [
                'user_id' => null,
                'polyclinic_id' => $polyclinics['POL-GIGI']->id,
                'name' => 'drg. Maya Safitri, Sp.KG (CONTOH)',
                'slug' => 'drg-maya-safitri-spkg',
                'sip_number' => 'SIP/446/301/2024',
                'specialization' => 'Spesialis Konservasi Gigi',
                'consultation_fee' => 120000,
                'bio' => 'Spesialis perawatan saluran akar dan restorasi estetika gigi.',
                'schedules' => [
                    ['day' => 1, 'start' => '09:00:00', 'end' => '13:00:00', 'quota' => 15, 'room' => 'Dental Unit 1'],
                    ['day' => 3, 'start' => '09:00:00', 'end' => '13:00:00', 'quota' => 15, 'room' => 'Dental Unit 1'],
                    ['day' => 4, 'start' => '09:00:00', 'end' => '13:00:00', 'quota' => 15, 'room' => 'Dental Unit 1'],
                ],
            ],
            [
                'user_id' => null,
                'polyclinic_id' => $polyclinics['POL-ANAK']->id,
                'name' => 'dr. Rizky Pratama, Sp.A (CONTOH)',
                'slug' => 'dr-rizky-pratama-spa',
                'sip_number' => 'SIP/446/401/2024',
                'specialization' => 'Spesialis Anak',
                'consultation_fee' => 150000,
                'bio' => 'Spesialis anak yang berfokus pada nutrisi anak, imunisasi, dan penanganan infeksi anak.',
                'schedules' => [
                    ['day' => 1, 'start' => '08:30:00', 'end' => '12:30:00', 'quota' => 20, 'room' => 'Poli Anak 1'],
                    ['day' => 2, 'start' => '08:30:00', 'end' => '12:30:00', 'quota' => 20, 'room' => 'Poli Anak 1'],
                    ['day' => 5, 'start' => '08:30:00', 'end' => '12:00:00', 'quota' => 15, 'room' => 'Poli Anak 1'],
                ],
            ],
        ];

        foreach ($doctorsData as $dData) {
            $schedules = $dData['schedules'];
            unset($dData['schedules']);

            $doctor = Doctor::updateOrCreate(
                ['slug' => $dData['slug']],
                array_merge($dData, ['is_active' => true])
            );

            foreach ($schedules as $sched) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $sched['day'],
                        'start_time' => $sched['start'],
                    ],
                    [
                        'end_time' => $sched['end'],
                        'max_quota' => $sched['quota'],
                        'room_name' => $sched['room'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // 4. 4 Kamar (10 Bed) CONTOH
        $roomsData = [
            [
                'code' => 'R-VVIP-01',
                'name' => 'Kamar Mawar 101 (CONTOH)',
                'ward_name' => 'Bangsal Mawar (VVIP)',
                'class_type' => 'VVIP',
                'price_per_day' => 1200000,
                'beds' => ['BED-VVIP-01'],
            ],
            [
                'code' => 'R-VIP-01',
                'name' => 'Kamar Melati 201 (CONTOH)',
                'ward_name' => 'Bangsal Melati (VIP)',
                'class_type' => 'VIP',
                'price_per_day' => 800000,
                'beds' => ['BED-VIP-01', 'BED-VIP-02'],
            ],
            [
                'code' => 'R-K1-01',
                'name' => 'Kamar Anggrek 301 (CONTOH)',
                'ward_name' => 'Bangsal Anggrek (Kelas 1)',
                'class_type' => 'Kelas 1',
                'price_per_day' => 450000,
                'beds' => ['BED-K1-01', 'BED-K1-02', 'BED-K1-03'],
            ],
            [
                'code' => 'R-ICU-01',
                'name' => 'Unit Perawatan Intensif (CONTOH)',
                'ward_name' => 'Bangsal Teratai (ICU)',
                'class_type' => 'ICU',
                'price_per_day' => 1500000,
                'beds' => ['BED-ICU-01', 'BED-ICU-02', 'BED-ICU-03', 'BED-ICU-04'],
            ],
        ];

        foreach ($roomsData as $rData) {
            $beds = $rData['beds'];
            unset($rData['beds']);

            $room = Room::updateOrCreate(
                ['code' => $rData['code']],
                array_merge($rData, ['is_active' => true])
            );

            foreach ($beds as $bedNumber) {
                Bed::firstOrCreate(
                    [
                        'room_id' => $room->id,
                        'bed_number' => $bedNumber,
                    ],
                    [
                        'status' => BedStatus::Available,
                    ]
                );
            }
        }

        // 5. 10 Obat CONTOH
        $medicinesData = [
            ['sku' => 'MED-001', 'name' => 'Paracetamol 500mg (CONTOH)', 'category' => 'Tablet', 'unit' => 'Strip', 'price' => 5000, 'stock' => 150, 'min_stock' => 20, 'expired_at' => '2027-12-31'],
            ['sku' => 'MED-002', 'name' => 'Amoxicillin 500mg (CONTOH)', 'category' => 'Tablet', 'unit' => 'Strip', 'price' => 12000, 'stock' => 80, 'min_stock' => 15, 'expired_at' => '2027-08-31'],
            ['sku' => 'MED-003', 'name' => 'Cetirizine 10mg (CONTOH)', 'category' => 'Tablet', 'unit' => 'Strip', 'price' => 8000, 'stock' => 100, 'min_stock' => 20, 'expired_at' => '2028-01-31'],
            ['sku' => 'MED-004', 'name' => 'Antasida Doen Tablet (CONTOH)', 'category' => 'Tablet', 'unit' => 'Strip', 'price' => 6000, 'stock' => 120, 'min_stock' => 20, 'expired_at' => '2027-06-30'],
            ['sku' => 'MED-005', 'name' => 'Omeprazole 20mg Kapsul (CONTOH)', 'category' => 'Tablet', 'unit' => 'Strip', 'price' => 18000, 'stock' => 90, 'min_stock' => 15, 'expired_at' => '2027-10-31'],
            ['sku' => 'MED-006', 'name' => 'Sanmol Sirup 60ml (CONTOH)', 'category' => 'Sirup', 'unit' => 'Botol', 'price' => 22000, 'stock' => 45, 'min_stock' => 10, 'expired_at' => '2027-04-30'],
            ['sku' => 'MED-007', 'name' => 'Ambroxol Sirup 60ml (CONTOH)', 'category' => 'Sirup', 'unit' => 'Botol', 'price' => 15000, 'stock' => 50, 'min_stock' => 10, 'expired_at' => '2027-05-31'],
            ['sku' => 'MED-008', 'name' => 'Salbutamol 2mg (CONTOH)', 'category' => 'Tablet', 'unit' => 'Strip', 'price' => 7500, 'stock' => 70, 'min_stock' => 15, 'expired_at' => '2027-09-30'],
            ['sku' => 'MED-009', 'name' => 'Betadine Antiseptic 30ml (CONTOH)', 'category' => 'Alkes', 'unit' => 'Botol', 'price' => 19000, 'stock' => 35, 'min_stock' => 10, 'expired_at' => '2028-06-30'],
            ['sku' => 'MED-010', 'name' => 'Kasa Steril 16x16 (CONTOH)', 'category' => 'Alkes', 'unit' => 'Pcs', 'price' => 8500, 'stock' => 200, 'min_stock' => 30, 'expired_at' => '2029-12-31'],
        ];

        foreach ($medicinesData as $med) {
            Medicine::updateOrCreate(
                ['sku' => $med['sku']],
                array_merge($med, ['is_active' => true])
            );
        }

        // 6. 5 Tindakan Medis CONTOH
        $actionsData = [
            ['code' => 'ACT-001', 'name' => 'Pemeriksaan & Konsultasi Dokter Umum (CONTOH)', 'polyclinic_id' => $polyclinics['POL-UMUM']->id, 'price' => 50000],
            ['code' => 'ACT-002', 'name' => 'Konsultasi Dokter Spesialis (CONTOH)', 'polyclinic_id' => $polyclinics['POL-DALAM']->id, 'price' => 150000],
            ['code' => 'ACT-003', 'name' => 'Pembersihan Karang Gigi / Scaling (CONTOH)', 'polyclinic_id' => $polyclinics['POL-GIGI']->id, 'price' => 250000],
            ['code' => 'ACT-004', 'name' => 'Nebulisasi Terapi Uap (CONTOH)', 'polyclinic_id' => $polyclinics['POL-ANAK']->id, 'price' => 75000],
            ['code' => 'ACT-005', 'name' => 'Penjahitan Luka Ringan / Hecting (CONTOH)', 'polyclinic_id' => $polyclinics['POL-UMUM']->id, 'price' => 100000],
        ];

        foreach ($actionsData as $act) {
            MedicalAction::updateOrCreate(
                ['code' => $act['code']],
                array_merge($act, ['is_active' => true])
            );
        }

        // 7. Pasien & Anggota Keluarga CONTOH
        $patientUser = User::where('email', 'pasien@aisyahmedika.id')->first();

        if ($patientUser) {
            $patient = Patient::updateOrCreate(
                ['medical_record_number' => 'RM-202611-0001'],
                [
                    'user_id' => $patientUser->id,
                    'nik' => '3201011234560001',
                    'name' => 'Budi Santoso (CONTOH)',
                    'gender' => 'L',
                    'birth_date' => '1988-06-15',
                    'blood_type' => 'O',
                    'allergies' => 'Penisilin, Seafood (Alergi Berat)',
                    'phone' => '081200000007',
                    'address' => 'Jl. Tebet Barat No. 12, Tebet, Jakarta Selatan',
                ]
            );

            PatientFamilyMember::firstOrCreate(
                [
                    'user_id' => $patientUser->id,
                    'patient_id' => $patient->id,
                ],
                [
                    'relationship' => 'diri_sendiri',
                ]
            );

            // Buat anak contoh
            $childPatient = Patient::updateOrCreate(
                ['medical_record_number' => 'RM-202611-0002'],
                [
                    'user_id' => null,
                    'nik' => '3201015566770002',
                    'name' => 'Siti Rahmawati (Anak - CONTOH)',
                    'gender' => 'P',
                    'birth_date' => '2019-08-20',
                    'blood_type' => 'O',
                    'allergies' => null,
                    'phone' => '081200000007',
                    'address' => 'Jl. Tebet Barat No. 12, Tebet, Jakarta Selatan',
                ]
            );

            PatientFamilyMember::firstOrCreate(
                [
                    'user_id' => $patientUser->id,
                    'patient_id' => $childPatient->id,
                ],
                [
                    'relationship' => 'anak',
                ]
            );
        }
    }
}
