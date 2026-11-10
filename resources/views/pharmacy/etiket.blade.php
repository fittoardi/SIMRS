<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiket Obat #RXP-{{ $prescription->id }} - RS Aisyah Medika</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            color: #111827;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Action bar for screen */
        .screen-toolbar {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #047857;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        /* Label thermal sticker container */
        .etiket-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            max-width: 320px; /* ~80mm width standard */
        }

        .etiket-card {
            background: #ffffff;
            border: 2px dashed #9ca3af;
            border-radius: 8px;
            padding: 14px;
            width: 100%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            page-break-after: always;
        }

        .etiket-card:last-child {
            page-break-after: auto;
        }

        .header {
            text-align: center;
            border-bottom: 1.5px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 11px;
            font-weight: 700;
            color: #1f2937;
        }

        .header p {
            font-size: 9px;
            color: #4b5563;
            margin-top: 2px;
            line-height: 1.2;
        }

        .meta-table {
            width: 100%;
            font-size: 10px;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta-table td.label {
            width: 70px;
            color: #374151;
            font-weight: 500;
        }

        .meta-table td.colon {
            width: 8px;
        }

        .meta-table td.val {
            font-weight: 700;
            color: #111827;
        }

        .med-box {
            border: 1.5px solid #111827;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            background: #fafafa;
            margin-bottom: 8px;
        }

        .med-name {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: #111827;
            margin-bottom: 4px;
        }

        .med-qty {
            font-size: 10px;
            color: #4b5563;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .signa-box {
            background: #111827;
            color: #ffffff;
            padding: 6px 4px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 9px;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dotted #9ca3af;
        }

        .footer .note {
            color: #4b5563;
            max-width: 160px;
            line-height: 1.2;
        }

        .footer .signature {
            text-align: right;
            font-weight: 700;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .screen-toolbar {
                display: none !important;
            }

            .etiket-wrapper {
                max-width: 100% !important;
                gap: 0 !important;
            }

            .etiket-card {
                border: 1px solid #000000 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                margin: 0 0 10mm 0 !important;
                page-break-after: always;
            }

            .signa-box {
                border: 1.5px solid #000000 !important;
                color: #000000 !important;
                background: #ffffff !important;
            }

            @page {
                size: 80mm auto;
                margin: 3mm;
            }
        }
    </style>
</head>
<body>

    <div class="screen-toolbar no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Etiket (Thermal)
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            ✕ Tutup Tab
        </button>
    </div>

    <div class="etiket-wrapper">
        @forelse($prescription->items as $index => $item)
            <div class="etiket-card">
                <div class="header">
                    <h2>INSTALASI FARMASI</h2>
                    <h1>RS AISYAH MEDIKA</h1>
                    <p>Jl. Raya Kesehatan No. 45, Telp: (021) 8899-7700<br>SIPA: 446/APT/2026/RSAM</p>
                </div>

                <table class="meta-table">
                    <tr>
                        <td class="label">No. Resep</td>
                        <td class="colon">:</td>
                        <td class="val">#RXP-{{ $prescription->id }} / Item {{ $index + 1 }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tgl Resep</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $prescription->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. RM</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $prescription->visit?->patient?->medical_record_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pasien</td>
                        <td class="colon">:</td>
                        <td class="val">
                            {{ $prescription->visit?->patient?->name ?? 'Pasien Umum' }}
                            @if($prescription->visit?->patient?->birth_date)
                                ({{ \Carbon\Carbon::parse($prescription->visit->patient->birth_date)->age }} th)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Dokter</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $prescription->doctor?->name ?? '-' }}</td>
                    </tr>
                </table>

                <div class="med-box">
                    <div class="med-name">{{ $item->medicine_name_snapshot }}</div>
                    <div class="med-qty">Jumlah: {{ $item->qty }} {{ $item->medicine?->unit ?? 'Pcs' }}</div>
                    <div class="signa-box">
                        {{ $item->dosage }}
                    </div>
                </div>

                <div class="footer">
                    <div class="note">
                        Simpan di tempat sejuk & kering terhindar dari sinar matahari langsung.<br>
                        Jauhkan dari jangkauan anak-anak.
                    </div>
                    <div class="signature">
                        Apoteker:<br>
                        <span style="font-size: 8px; color: #4b5563;">RS Aisyah Medika</span><br>
                        <strong>{{ $prescription->pharmacist?->name ?? 'Apt. Jaga' }}</strong>
                    </div>
                </div>
            </div>
        @empty
            <div class="etiket-card" style="text-align: center; color: #6b7280;">
                <p>Tidak ada obat dalam resep ini.</p>
            </div>
        @endforelse
    </div>

    <script>
        // Otomatis picu dialog print browser saat halaman selesai dimuat
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>

