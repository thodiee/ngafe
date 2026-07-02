<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Create Snap transaction.
     * 
     * @param \App\Models\Order $order
     * @return array
     */
    public static function createSnapTransaction($order)
    {
        $mode = Setting::get('midtrans_mode', 'simulation');
        $serverKey = Setting::get('midtrans_server_key');
        $isProduction = Setting::get('midtrans_is_production', '0') === '1';

        // Check if we should use simulation mode
        if ($mode === 'simulation' || empty($serverKey)) {
            // Return simulation payload
            return [
                'token' => 'simulated-token-' . uniqid(),
                'redirect_url' => route('self-service.simulation', $order->order_reference)
            ];
        }

        // Real Midtrans Snap call
        $baseUrl = $isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_reference,
                'gross_amount' => (int) $order->total_amount,
            ],
            'credit_card' => [
                'secure' => true
            ],
            'customer_details' => [
                'first_name' => 'Meja ' . ($order->table->table_number ?? 'Dine In'),
                'email' => 'customer@ngafe.com',
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withBasicAuth($serverKey, '')
            ->post($baseUrl, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Midtrans API Fail: ' . $response->body());
            return [
                'error' => true,
                'message' => 'Gagal menghubungi server pembayaran Midtrans.'
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Terjadi kesalahan sistem pembayaran.'
            ];
        }
    }
}
