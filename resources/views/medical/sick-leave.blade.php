<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Sakit — {{ $certificate->certificate_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Screen Toolbar */
        .screen-toolbar {
            width: 100%;
            max-width: 720px;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
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

        /* A4/A5 Printable Sheet */
        .certificate-sheet {
            width: 100%;
            max-width: 720px;
            background: #ffffff;
            padding: 48px 56px;
            border-radius: 8px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        /* Kop Surat RS */
        .kop-header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 14px;
            margin-bottom: 24px;
            gap: 18px;
        }

        .kop-logo {
            width: 74px;
            height: 74px;
            border-radius: 12px;
            background-color: #0d9488;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: bold;
            font-family: sans-serif;
            font-size: 32px;
            shrink-0: 0;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0f766e;
            text-transform: uppercase;
            margin-bottom: 3px;
            font-family: 'Arial', sans-serif;
        }

        .kop-text h2 {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 3px;
            font-family: 'Arial', sans-serif;
        }

        .kop-text p {
            font-size: 11px;
            color: #475569;
            line-height: 1.4;
            font-family: 'Arial', sans-serif;
        }

        /* Judul Surat */
        .title-section {
            text-align: center;
            margin-bottom: 26px;
        }

        .title-section h3 {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .title-section p {
            font-size: 13px;
            font-style: italic;
            color: #334155;
        }

        /* Body Prose */
        .content-body {
            font-size: 14px;
            line-height: 1.7;
            text-align: justify;
        }

        .patient-table {
            width: 100%;
            margin: 14px 0 18px 0;
            border-collapse: collapse;
        }

        .patient-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 14px;
        }

        .patient-table td.label-col {
            width: 170px;
            color: #334155;
        }

        .patient-table td.colon-col {
            width: 16px;
            text-align: center;
        }

        .patient-table td.value-col {
            font-weight: 600;
            color: #0f172a;
        }

        .rest-highlight-box {
            background-color: #f8fafc;
            border-left: 4px solid #0d9488;
            padding: 12px 18px;
            margin: 18px 0;
            border-radius: 4px;
        }

        .rest-highlight-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .notes-section {
            margin-top: 12px;
            font-size: 13px;
            color: #334155;
            font-style: italic;
        }

        /* Footer & Signatures */
        .footer-section {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }

        .qr-verification-box {
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            border-radius: 8px;
            max-width: 320px;
            background: #ffffff;
        }

        .qr-verification-box .qr-code {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }

        .qr-verification-box .qr-text {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }

        .qr-verification-box .qr-text strong {
            display: block;
            color: #0f172a;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .doctor-signature-box {
            text-align: center;
            min-width: 220px;
        }

        .doctor-signature-box .date-line {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .doctor-signature-box .role-line {
            font-size: 13px;
            margin-bottom: 60px;
        }

        .doctor-signature-box .doctor-name {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }

        .doctor-signature-box .sip-line {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }

        /* Watermark Digital Validasi */
        .digital-badge {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 10px;
            color: #64748b;
        }

        /* Media Print Rules */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }

            .screen-toolbar {
                display: none !important;
            }

            .certificate-sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 25px 35px;
                max-width: 100%;
            }

            .rest-highlight-box {
                background-color: transparent;
                border-left: 3px solid #000000;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Toolbar (Hidden on Print) -->
    <div class="screen-toolbar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="javascript:history.back()" class="btn btn-secondary">
                ← Kembali
            </a>
            <span style="font-size: 12px; color: #64748b;">Ukuran Cetak Standar: A4 / A5</span>
        </div>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Surat Sakit
        </button>
    </div>

    <!-- Official Certificate Printable Sheet -->
    <div class="certificate-sheet">
        <!-- Kop Surat -->
        <div class="kop-header">
            <div class="kop-logo">
                +
            </div>
            <div class="kop-text">
                <h1>Rumah Sakit Aisyah Medika</h1>
                <h2>Instalasi Rawat Jalan & Rekam Medis Terpadu</h2>
                <p>
                    Jl. RS Fatmawati Raya No. 45, Cilandak, Jakarta Selatan 12430<br>
                    Telepon: (021) 8765-4321 • Email: layanan@aisyahmedika.co.id • Website: www.aisyahmedika.co.id
                </p>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="title-section">
            <h3>SURAT KETERANGAN SAKIT</h3>
            <p>Nomor: {{ $certificate->certificate_number }}</p>
        </div>

        <!-- Body Isi Surat -->
        <div class="content-body">
            <p>
                Yang bertanda tangan di bawah ini, Dokter Pemeriksa Rumah Sakit Aisyah Medika, dengan mengingat sumpah jabatan menerangkan dengan sesungguhnya bahwa:
            </p>

            <table class="patient-table">
                <tr>
                    <td class="label-col">Nama Pasien</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $certificate->patient?->name }}</td>
                </tr>
                <tr>
                    <td class="label-col">No. Rekam Medis</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $certificate->patient?->medical_record_number }}</td>
                </tr>
                <tr>
                    <td class="label-col">NIK</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $certificate->patient?->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Jenis Kelamin / Usia</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        {{ $certificate->patient?->gender === 'male' || $certificate->patient?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} /
                        {{ $certificate->patient?->birth_date ? \Carbon\Carbon::parse($certificate->patient->birth_date)->age . ' Tahun' : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Pekerjaan / Instansi</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $certificate->patient?->occupation ?? 'Umum / Karyawan' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Alamat</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $certificate->patient?->address ?? '-' }}</td>
                </tr>
            </table>

            <p>
                Berdasarkan hasil pemeriksaan klinis yang telah dilakukan di <strong>{{ $certificate->visit?->polyclinic?->name ?? 'Poliklinik Rawat Jalan' }}</strong>, pasien tersebut dinyatakan dalam kondisi <strong>SAKIT</strong> sehingga memerlukan istirahat medis selama:
            </p>

            <div class="rest-highlight-box">
                <p>
                    <strong>{{ $certificate->days_count }} Hari</strong>
                    terhitung mulai tanggal:
                    <strong>{{ \Carbon\Carbon::parse($certificate->start_date)->translatedFormat('d F Y') }}</strong>
                    sampai dengan tanggal:
                    <strong>{{ \Carbon\Carbon::parse($certificate->end_date)->translatedFormat('d F Y') }}</strong>.
                </p>
            </div>

            @if($certificate->notes)
                <div class="notes-section">
                    <strong>Catatan / Anjuran Dokter:</strong> {{ $certificate->notes }}
                </div>
            @endif

            <p style="margin-top: 14px;">
                Demikian surat keterangan ini kami buat dengan sesungguhnya atas dasar rekam medis pemeriksaan klinis yang dapat dipertanggungjawabkan, untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- Tanda Tangan & QR Verification -->
        <div class="footer-section">
            <!-- QR Anti-Pemalsuan -->
            <div class="qr-verification-box">
                <div class="qr-code">
                    {!! $qrSvg !!}
                </div>
                <div class="qr-text">
                    <strong>E-VERIFIKASI RESMI</strong>
                    Pindai QR Code untuk memeriksa keaslian surat melalui portal publik resmi RS Aisyah Medika.<br>
                    <span style="font-family: monospace; font-size: 9px; color: #64748b;">Token: {{ substr($certificate->verification_token, 0, 16) }}...</span>
                </div>
            </div>

            <!-- TTD Dokter -->
            <div class="doctor-signature-box">
                <div class="date-line">
                    Jakarta Selatan, {{ \Carbon\Carbon::parse($certificate->issued_at ?? now())->translatedFormat('d F Y') }}
                </div>
                <div class="role-line">
                    Dokter Pemeriksa,
                </div>
                <div class="doctor-name">
                    {{ $certificate->doctor?->name ?? 'Dokter Pemeriksa' }}
                </div>
                <div class="sip-line">
                    SIP: {{ $certificate->doctor?->sip_number ?? 'SIP-RSAM-'.date('Y') }}
                </div>
            </div>
        </div>

        <!-- Watermark / Footer Track -->
        <div class="digital-badge">
            <span>Dokumen Rekam Medis Sah Elektronik — SIMRS RS Aisyah Medika</span>
            <span>ID Registrasi: {{ $certificate->visit?->visit_number ?? 'VIS-'.date('Ymd') }}</span>
        </div>
    </div>

</body>
</html>
