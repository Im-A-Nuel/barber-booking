<?php

namespace App\Services;

use App\Booking;
use App\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create payment with Midtrans Snap
     *
     * @param Booking $booking
     * @param string $method
     * @return array
     * @throws \Exception
     */
    public function createMidtransPayment(Booking $booking, $method = 'gateway')
    {
        // Generate unique order ID
        $orderId = 'BOOKING-' . $booking->id . '-' . time();

        // Prepare transaction parameters
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $booking->service->price,
            ],
            'customer_details' => [
                'first_name' => $booking->customer->name,
                'email' => $booking->customer->email,
            ],
            'item_details' => [
                [
                    'id' => $booking->service->id,
                    'price' => (int) $booking->service->price,
                    'quantity' => 1,
                    'name' => $booking->service->name,
                ]
            ],
            'enabled_payments' => [
                'gopay', 'shopeepay', 'other_qris',
                'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
                'echannel', 'credit_card'
            ],
        ];

        try {
            // Get Snap token from Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Create payment record in database
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->service->price,
                'method' => $method,
                'payment_type' => 'gateway',
                'gateway_name' => 'midtrans',
                'transaction_id' => $orderId,
                'status' => 'pending',
                'expires_at' => now()->addHours(24), // Midtrans default expiry: 24 hours
            ]);

            Log::info('Midtrans payment created', [
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'amount' => $booking->service->price
            ]);

            return [
                'snap_token' => $snapToken,
                'payment' => $payment,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create Midtrans payment', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans notification callback
     *
     * @param mixed $notificationData
     * @return Payment
     * @throws \Exception
     */
    public function handleMidtransCallback($notificationData)
    {
        try {
            // Create notification object
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = isset($notification->fraud_status) ? $notification->fraud_status : null;

            Log::info('Midtrans callback received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Find payment by transaction ID
            $payment = Payment::where('transaction_id', $orderId)->firstOrFail();

            // Update payment status based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Challenge by FDS, wait for admin approval
                    $payment->status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    // Payment accepted
                    $payment->status = 'paid';
                    $payment->paid_at = now();
                }
            } else if ($transactionStatus == 'settlement') {
                // Payment settled
                $payment->status = 'paid';
                $payment->paid_at = now();
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // Payment failed/cancelled
                $payment->status = 'failed';
            } else if ($transactionStatus == 'pending') {
                // Payment pending
                $payment->status = 'pending';
            }

            // Store gateway response
            $payment->gateway_response = json_encode($notification);
            $payment->save();

            // Auto-update booking status if payment is paid
            if ($payment->status === 'paid' && $payment->booking->status === Booking::STATUS_CONFIRMED) {
                $payment->booking->status = Booking::STATUS_COMPLETED;
                $payment->booking->save();

                Log::info('Booking auto-completed after payment', [
                    'booking_id' => $payment->booking_id
                ]);
            }

            return $payment;

        } catch (\Exception $e) {
            Log::error('Failed to handle Midtrans callback', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get payment status from Midtrans
     *
     * @param string $orderId
     * @return mixed
     */
    public function getPaymentStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            Log::error('Failed to get payment status from Midtrans', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
