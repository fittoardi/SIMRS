<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pasien {{ $patient->medical_record_number }} — RS Aisyah Medika</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Toolbar Layar */
        .screen-toolbar {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: #0d9488;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0f766e;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .cards-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
            align-items: center;
        }

        /* Ukuran Kartu Standar ISO CR80 (85.6mm x 54mm) disimulasikan di layar */
        .patient-card {
            width: 420px;
            min-height: 250px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.2), 0 8px 10px -6px rgba(13, 148, 136, 0.1);
            background: #ffffff;
            border: 1px solid #cbd5e1;
            position: relative;
            page-break-inside: avoid;
        }

        /* Front Side Styling */
        .card-front {
            background: linear-gradient(135deg, #042f2e 0%, #0f766e 50%, #115e59 100%);
            color: #ffffff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: #ffffff;
            color: #0d9488;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 20px;
            line-height: 1;
        }

        .brand-text h2 {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.02em;
            text-transform: uppercase;
        }

        .brand-text p {
            font-size: 9px;
            color: #99f6e4;
            letter-spacing: 0.05em;
        }

        .card-type-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            gap: 12px;
        }

        .patient-info {
            flex: 1;
        }

        .patient-name {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .mr-number-box {
            display: inline-block;
            background: #ffffff;
            color: #0f766e;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 900;
            font-size: 15px;
            padding: 3px 10px;
            border-radius: 8px;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 10px;
            font-size: 10px;
            color: #ccfbf1;
        }

        .details-grid strong {
            color: #ffffff;
        }

        .qr-box {
            background: #ffffff;
            padding: 6px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            shrink: 0;
        }

        .qr-box p {
            font-size: 8px;
            color: #475569;
            font-weight: 700;
            margin-top: 3px;
            text-transform: uppercase;
        }

        .card-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #99f6e4;
        }

        .allergy-alert {
            background: #e11d48;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        /* Back Side Styling */
        .card-back {
            background: #ffffff;
            color: #1e293b;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .rules-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 4px;
        }

        .rules-list {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.45;
            padding-left: 14px;
        }

        .rules-list li {
            margin-bottom: 5px;
        }

        .hospital-contact-box {
            margin-top: 10px;
            padding: 8px 10px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-left {
            color: #334155;
            font-weight: 600;
        }

        .contact-right {
            text-align: right;
            color: #0d9488;
            font-weight: 800;
        }

        /* Print Specifics */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .screen-toolbar {
                display: none !important;
            }

            .cards-wrapper {
                gap: 16px;
            }

            .patient-card {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Action Toolbar -->
    <div class="screen-toolbar">
        <div>
            <h1 style="font-size: 13px; font-weight: 800; color: #0f172a;">Cetak Kartu Berobat</h1>
            <p style="font-size: 11px; color: #64748b;">No. RM: {{ $patient->medical_record_number }}</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak Kartu
            </button>
            <button onclick="window.history.length > 1 ? window.history.back() : window.close()" class="btn btn-secondary">
                Tutup
            </button>
        </div>
    </div>

    <!-- Cards Container -->
    <div class="cards-wrapper">

        <!-- TAMPAK DEPAN (FRONT) -->
        <div class="patient-card card-front">
            <!-- Header -->
            <div class="card-header">
                <div class="brand">
                    <div class="brand-icon">+</div>
                    <div class="brand-text">
                        <h2>RS Aisyah Medika</h2>
                        <p>KARTU BEROBAT PASIEN</p>
                    </div>
                </div>
                <div class="card-type-badge">
                    {{ $patient->bpjs_number ? 'BPJS / JKN' : ($patient->insurance_name ? 'ASURANSI' : 'UMUM') }}
                </div>
            </div>

            <!-- Body Info -->
            <div class="card-body">
                <div class="patient-info">
                    <div class="mr-number-box">
                        {{ $patient->medical_record_number }}
                    </div>
                    <h3 class="patient-name">{{ $patient->name }}</h3>

                    <div class="details-grid">
                        <div>NIK: <strong>{{ $patient->nik ?: '-' }}</strong></div>
                        <div>Gol. Darah: <strong>{{ $patient->blood_type ?: '-' }}</strong></div>
                        <div>Tgl Lahir: <strong>{{ $patient->birth_date ? $patient->birth_date->format('d/m/Y') : '-' }}</strong></div>
                        <div>Gender: <strong>{{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong></div>
                    </div>
                </div>

                <!-- QR Code No. RM -->
                <div class="qr-box">
                    <div style="width: 86px; height: 86px; display: flex; align-items: center; justify-content: center;">
                        {!! $qrSvg !!}
                    </div>
                    <p>SCAN KIOSK</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer">
                @if($patient->allergies)
                    <div class="allergy-alert">
                        ⚠️ ALERGI: {{ Str::limit($patient->allergies, 28) }}
                    </div>
                @else
                    <div>Terdaftar: {{ $patient->created_at ? $patient->created_at->format('d M Y') : date('d M Y') }}</div>
                @endif
                <div style="font-weight: 700;">RS AISYAH MEDIKA</div>
            </div>
        </div>

        <!-- TAMPAK BELAKANG (BACK) -->
        <div class="patient-card card-back">
            <div>
                <div class="rules-title">Ketentuan Pemegang Kartu Berobat</div>
                <ol class="rules-list">
                    <li>Kartu ini adalah identitas resmi berobat pasien di seluruh unit pelayanan RS Aisyah Medika.</li>
                    <li>Selalu bawa kartu ini atau tunjukkan QR Code pada mesin Kiosk Mandiri lobi untuk check-in otomatis tanpa antre di loket admisi.</li>
                    <li>Kartu ini hanya berlaku untuk nama pasien yang tercantum dan tidak dapat dipindahtangankan.</li>
                    <li>Simpan kartu dengan baik. Apabila hilang, segera laporkan ke bagian admisi RS.</li>
                </ol>
            </div>

            <div>
                <div class="hospital-contact-box">
                    <div class="contact-left">
                        <p style="font-weight: 800; color: #0f172a;">RS AISYAH MEDIKA</p>
                        <p style="font-size: 8.5px; color: #64748b;">Jl. Sehat Medika No. 10 Jakarta</p>
                    </div>
                    <div class="contact-right">
                        <p>Call Center: (021) 8765-4321</p>
                        <p style="color: #e11d48;">IGD 24 Jam: (021) 8765-4322</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
