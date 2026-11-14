<?php

namespace App\Http\Controllers\Webhooks;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\BillingService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected BillingService $billingService,
        protected WhatsAppNotificationService $waService
    ) {}

    /**
     * Handle webhook notifikasi pembayaran dari Payment Gateway (Midtrans / Xendit).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Payment Gateway Webhook Received', [
            'ip' => $request->ip(),
            'payload' => $payload,
        ]);

        // 1. Ekstraksi Data Utama dari Payload
        $orderId = $request->input('order_id') ?? $request->input('external_id');
        $statusCode = (string) ($request->input('status_code') ?? '');
        $grossAmount = (string) ($request->input('gross_amount') ?? $request->input('amount') ?? '');
        $transactionStatus = strtolower((string) ($request->input('transaction_status') ?? $request->input('status') ?? ''));
        $fraudStatus = strtolower((string) ($request->input('fraud_status') ?? 'accept'));
        $paymentType = strtolower((string) ($request->input('payment_type') ?? $request->input('payment_method') ?? 'qris'));
        $transactionId = (string) ($request->input('transaction_id') ?? $request->input('id') ?? ('TX-'.bin2hex(random_bytes(6))));

        if (empty($orderId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing order_id / external_id in payload.',
            ], 400);
        }

        // 2. Verifikasi Signature Cryptographic (Midtrans SHA-512)
        $serverKey = (string) config('services.midtrans.server_key');
        $signatureKey = (string) $request->input('signature_key');

        if (! empty($serverKey) && ! empty($signatureKey)) {
            $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
            if (! hash_equals($expectedSignature, $signatureKey)) {
                Log::warning('Payment Webhook Rejected: Invalid signature', [
                    'order_id' => $orderId,
                    'given' => $signatureKey,
                    'expected' => $expectedSignature,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature key.',
                ], 403);
            }
        }

        // 3. Cari Data Invoice Pasien
        $invoice = Invoice::where('number', $orderId)->first();

        if (! $invoice) {
            Log::warning("Payment Webhook: Invoice {$orderId} not found.");

            return response()->json([
                'status' => 'error',
                'message' => "Invoice {$orderId} not found in database.",
            ], 404);
        }

        // 4. Idempotency Check (Cegah Duplikasi Transaksi)
        $isSuccess = in_array($transactionStatus, ['settlement', 'capture', 'paid', 'completed'])
            && $fraudStatus === 'accept';

        if ($isSuccess) {
            if ($invoice->status === PaymentStatus::Paid) {
                return response()->json([
                    'status' => 'already_processed',
                    'message' => "Invoice {$orderId} is already marked as PAID.",
                ], 200);
            }

            // Eksekusi Pelunasan Tagihan (BillingService otomatis mengirim notifikasi WhatsApp kwitansi)
            $payment = $this->billingService->payWithOnlineGateway(
                invoice: $invoice,
                method: $paymentType,
                providerRef: $transactionId,
                extraPayload: $payload
            );

            return response()->json([
                'status' => 'success',
                'message' => "Invoice {$orderId} settled successfully.",
                'payment_id' => $payment->id,
            ], 200);
        }

        // 5. Penanganan Transaksi Gagal / Kadaluarsa
        if (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
            Log::info("Payment Webhook: Invoice {$orderId} marked as {$transactionStatus}.");

            return response()->json([
                'status' => 'recorded',
                'message' => "Transaction status {$transactionStatus} acknowledged.",
            ], 200);
        }

        return response()->json([
            'status' => 'acknowledged',
            'message' => "Transaction status {$transactionStatus} received.",
        ], 200);
    }
}
