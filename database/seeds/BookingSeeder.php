<?php

use App\Booking;
use App\User;
use App\Service;
use App\Stylist;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = User::where('role', 'customer')->first();
        $service = Service::where('is_active', true)->first();
        $stylist = Stylist::where('is_active', true)->first();

        if (!$customer || !$service || !$stylist) {
            $this->command->warn('Skipping BookingSeeder: Required data not found (customer, service, or stylist).');
            return;
        }

        // Create bookings with various statuses

        // 1. Pending booking for tomorrow
        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'stylist_id' => $stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => Carbon::parse('09:00:00')->addMinutes($service->duration_minutes)->format('H:i:s'),
            'status' => Booking::STATUS_PENDING,
            'notes' => 'Booking untuk besok, menunggu konfirmasi.',
        ]);

        // 2. Confirmed booking for next week
        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'stylist_id' => $stylist->id,
            'booking_date' => Carbon::now()->addWeek()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => Carbon::parse('14:00:00')->addMinutes($service->duration_minutes)->format('H:i:s'),
            'status' => Booking::STATUS_CONFIRMED,
            'notes' => 'Sudah dikonfirmasi oleh admin.',
        ]);

        // 3. Completed booking from last week
        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'stylist_id' => $stylist->id,
            'booking_date' => Carbon::now()->subWeek()->format('Y-m-d'),
            'start_time' => '10:30:00',
            'end_time' => Carbon::parse('10:30:00')->addMinutes($service->duration_minutes)->format('H:i:s'),
            'status' => Booking::STATUS_COMPLETED,
            'notes' => 'Booking selesai dengan baik.',
        ]);

        // 4. Cancelled booking
        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'stylist_id' => $stylist->id,
            'booking_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => Carbon::parse('11:00:00')->addMinutes($service->duration_minutes)->format('H:i:s'),
            'status' => Booking::STATUS_CANCELLED,
            'notes' => 'Dibatalkan karena ada keperluan mendadak.',
        ]);

        // 5. Another confirmed booking for next month
        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'stylist_id' => $stylist->id,
            'booking_date' => Carbon::now()->addMonth()->format('Y-m-d'),
            'start_time' => '15:30:00',
            'end_time' => Carbon::parse('15:30:00')->addMinutes($service->duration_minutes)->format('H:i:s'),
            'status' => Booking::STATUS_CONFIRMED,
            'notes' => null,
        ]);

        $this->command->info('Created 5 dummy bookings.');
    }
}
