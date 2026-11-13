<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $invoice->number }} — RS Aisyah Medika</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Courier New", Courier, monospace, -apple-system, sans-serif;
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

        /* Screen Action Toolbar */
        .screen-toolbar {
            width: 100%;
            max-width: 360px;
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
        }

        .btn-primary {
            background-color: #0d9488;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        /* Thermal Receipt Container (80mm standard width) */
        .receipt-card {
            background: #ffffff;
            width: 100%;
            max-width: 360px;
            padding: 18px 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.35;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 11px;
            font-weight: 700;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 9px;
            color: #4b5563;
            margin-top: 2px;
        }

        .divider {
            border-top: 1px dashed #4b5563;
            margin: 8px 0;
        }

        .divider-double {
            border-top: 2px dashed #111827;
            margin: 8px 0;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .meta-row .label {
            color: #4b5563;
        }

        .meta-row .val {
            font-weight: 700;
            text-align: right;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .items-table th {
            text-align: left;
            font-weight: 700;
            padding-bottom: 4px;
            border-bottom: 1px dashed #9ca3af;
        }

        .items-table th.num {
            text-align: right;
        }

        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .items-table td.num {
            text-align: right;
            white-space: nowrap;
        }

        .item-title {
            font-weight: 700;
        }

        .item-sub {
            font-size: 9px;
            color: #4b5563;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }

        .total-row.grand {
            font-size: 14px;
            font-weight: 900;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #111827;
        }

        .status-badge {
            display: inline-block;
            text-align: center;
            width: 100%;
            padding: 4px 0;
            margin: 6px 0;
            font-weight: 900;
            font-size: 13px;
            letter-spacing: 1px;
            border: 1.5px solid #111827;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #f0fdf4;
            color: #15803d;
            border-color: #15803d;
        }

        .status-unpaid {
            background-color: #fffbeb;
            color: #b45309;
            border-color: #b45309;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #4b5563;
            margin-top: 10px;
            line-height: 1.3;
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

            .receipt-card {
                max-width: 100% !important;
                width: 80mm !important;
                box-shadow: none !important;
                border: none !important;
                padding: 4mm !important;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="screen-toolbar no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Kwitansi
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            ✕ Tutup Tab
        </button>
    </div>

    <div class="receipt-card">
        <div class="header">
            <h2>INSTALASI KASIR & KEUANGAN</h2>
            <h1>RS AISYAH MEDIKA</h1>
            <p>Jl. Raya Kesehatan No. 45, Jakarta Selatan<br>Telp: (021) 8765-4321 / IGD: 119</p>
        </div>

        <div class="divider-double"></div>

        <div class="meta-row">
            <span class="label">No. Tagihan</span>
            <span class="val">{{ $invoice->number }}</span>
        </div>
        <div class="meta-row">
            <span class="label">Tanggal/Waktu</span>
            <span class="val">{{ $invoice->updated_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="meta-row">
            <span class="label">No. RM</span>
            <span class="val">{{ $invoice->patient?->medical_record_number ?? '-' }}</span>
        </div>
        <div class="meta-row">
            <span class="label">Nama Pasien</span>
            <span class="val">{{ $invoice->patient?->name }}</span>
        </div>
        <div class="meta-row">
            <span class="label">Layanan/Poli</span>
            <span class="val">{{ $invoice->visit?->polyclinic?->name ?? 'Poliklinik' }}</span>
        </div>
        <div class="meta-row">
            <span class="label">Dokter</span>
            <span class="val">{{ $invoice->visit?->doctor?->name ?? '-' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Rincian Item Tagihan -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Rincian Layanan</th>
                    <th class="num">Jml</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="item-title">{{ $item->name_snapshot }}</div>
                            <div class="item-sub">@ Rp {{ number_format((float) $item->price_snapshot, 0, ',', '.') }}</div>
                        </td>
                        <td class="num">{{ $item->qty }}</td>
                        <td class="num">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #9ca3af;">Tidak ada rincian item</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Ringkasan Finansial -->
        <div class="total-row">
            <span>Subtotal Biaya</span>
            <span>Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</span>
        </div>

        @if((float) $invoice->discount > 0)
            <div class="total-row" style="color: #dc2626;">
                <span>Diskon / Potongan</span>
                <span>- Rp {{ number_format((float) $invoice->discount, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="total-row grand">
            <span>TOTAL TAGIHAN</span>
            <span>Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</span>
        </div>

        <!-- Status & Rincian Pembayaran -->
        @if($invoice->status->value === 'paid')
            <div class="status-badge status-paid">
                *** LUNAS ***
            </div>

            @php
                $raw = $latestPayment?->raw_payload ?? [];
                $methodName = match($latestPayment?->method ?? 'cash') {
                    'cash' => 'TUNAI (CASH)',
                    'qris' => 'QRIS (NON-TUNAI)',
                    'bca_va', 'bni_va', 'mandiri_va' => 'VIRTUAL ACCOUNT',
                    default => strtoupper($latestPayment?->method ?? 'TUNAI'),
                };
            @endphp

            <div class="total-row">
                <span>Metode Pembayaran</span>
                <span style="font-weight: 700;">{{ $methodName }}</span>
            </div>

            @if(!empty($raw['amount_tendered']))
                <div class="total-row">
                    <span>Uang Diterima</span>
                    <span>Rp {{ number_format((float) $raw['amount_tendered'], 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Uang Kembalian</span>
                    <span style="font-weight: 700;">Rp {{ number_format((float) ($raw['change'] ?? 0), 0, ',', '.') }}</span>
                </div>
            @endif
        @else
            <div class="status-badge status-unpaid">
                BELUM DIBAYAR
            </div>
        @endif

        <div class="divider-double"></div>

        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda.</p>
            <p>Semoga lekas sembuh.</p>
            <p style="margin-top: 4px; font-size: 8px;">Struk ini merupakan bukti pembayaran sah yang dicetak secara sistem komputerisasi SIMRS RS Aisyah Medika.</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>

