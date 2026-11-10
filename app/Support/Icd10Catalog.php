<?php

namespace App\Support;

class Icd10Catalog
{
    /**
     * Daftar diagnosa ICD-10 umum poliklinik rawat jalan (Standar Kemenkes RI).
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'A09' => 'A09 - Diare & Gastroenteritis Infeksi',
            'B01' => 'B01 - Varisela (Cacar Air)',
            'B05' => 'B05 - Campak (Morbili)',
            'E10' => 'E10 - Diabetes Melitus Tipe 1',
            'E11' => 'E11 - Diabetes Melitus Tipe 2',
            'E78' => 'E78 - Gangguan Metabolisme Lipoprotein (Dislipidemia / Kolesterol)',
            'I10' => 'I10 - Hipertensi Esensial (Primer)',
            'I20' => 'I20 - Angina Pectoris',
            'I50' => 'I50 - Gagal Jantung (Heart Failure)',
            'J00' => 'J00 - Nasofaringitis Akut (Common Cold / Batuk Pilek)',
            'J02' => 'J02 - Faringitis Akut (Radang Tenggorokan)',
            'J03' => 'J03 - Tonsilitis Akut (Amandel)',
            'J06' => 'J06 - Infeksi Saluran Pernapasan Akut Atas (ISPA)',
            'J18' => 'J18 - Pneumonia',
            'J20' => 'J20 - Bronkitis Akut',
            'J45' => 'J45 - Asma Bronkial',
            'K02' => 'K02 - Karies Gigi (Gigi Berlubang)',
            'K04' => 'K04 - Penyakit Pulpa dan Jaringan Periapikal',
            'K05' => 'K05 - Gingivitis dan Penyakit Periodontal',
            'K29' => 'K29 - Gastritis dan Duodenitis (Maag / Asam Lambung)',
            'K30' => 'K30 - Dispepsia',
            'K59' => 'K59 - Konstipasi (Sembelit)',
            'L20' => 'L20 - Dermatitis Atopik (Eksim)',
            'L30' => 'L30 - Dermatitis Lainnya',
            'M10' => 'M10 - Gout (Asam Urat)',
            'M15' => 'M15 - Poliartrosis / Osteoartritis',
            'M54' => 'M54 - Nyeri Punggung Bawah (Low Back Pain / Myalgia)',
            'R50' => 'R50 - Demam (Febris)',
            'R51' => 'R51 - Nyeri Kepala (Cephalea / Sakit Kepala)',
            'Z00' => 'Z00 - Pemeriksaan Kesehatan Umum (General Medical Check-Up)',
        ];
    }
}
