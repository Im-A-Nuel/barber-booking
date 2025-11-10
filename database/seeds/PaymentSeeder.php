<?php

use Illuminate\Database\Seeder;
use App\Booking;
use App\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some confirmed or completed bookings to add payments
        $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
            ->limit(5)
            ->get();

        $methods = ['cash', 'transfer', 'e-wallet', 'debit_card', 'credit_card'];
        $statuses = ['paid', 'pending'];

        foreach ($bookings as $booking) {
            // Only create payment if doesn't exist
            if (!$booking->payment) {
                $status = $statuses[array_rand($statuses)];

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->service->price,
                    'method' => $methods[array_rand($methods)],
                    'status' => $status,
                    'paid_at' => $status === 'paid' ? now()->subDays(rand(0, 5)) : null,
                ]);
            }
        }

        echo "Payment seeder completed. Created payments for " . $bookings->count() . " bookings.\n";
    }
}
