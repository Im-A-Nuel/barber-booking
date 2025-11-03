<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Service;
use App\Stylist;
use App\Schedule;
use App\Http\Requests\Booking\StoreBookingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of customer's bookings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings = Booking::with(['service', 'stylist.user'])
            ->where('customer_id', auth()->id())
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking - Step 1: Select Service.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('bookings.create', compact('services'));
    }

    /**
     * Step 2: Select stylist for the service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectStylist(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($request->service_id);

        // Get stylists who have schedules (available to work)
        $stylists = Stylist::with('user')
            ->where('is_active', true)
            ->whereHas('schedules', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('id')
            ->get();

        return view('bookings.select-stylist', compact('service', 'stylists'));
    }

    /**
     * Step 3: Select date and time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectDateTime(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'stylist_id' => 'required|exists:stylists,id',
        ]);

        $service = Service::findOrFail($request->service_id);
        $stylist = Stylist::with('user')->findOrFail($request->stylist_id);

        return view('bookings.select-datetime', compact('service', 'stylist'));
    }

    /**
     * Get available time slots for a specific date (AJAX).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'stylist_id' => 'required|exists:stylists,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $service = Service::findOrFail($request->service_id);
        $stylistId = $request->stylist_id;
        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday

        // Get stylist schedule for this day
        $schedule = Schedule::where('stylist_id', $stylistId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Stylist tidak tersedia pada hari ini.',
                'slots' => []
            ]);
        }

        // Get existing bookings for this stylist on this date
        $existingBookings = Booking::where('stylist_id', $stylistId)
            ->where('booking_date', $date->format('Y-m-d'))
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->get();

        // Calculate available slots
        $slots = $this->calculateAvailableSlots(
            $schedule->start_time,
            $schedule->end_time,
            $service->duration_minutes,
            $existingBookings,
            $date
        );

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Calculate available time slots.
     *
     * @param  string  $scheduleStart
     * @param  string  $scheduleEnd
     * @param  int  $durationMinutes
     * @param  \Illuminate\Support\Collection  $existingBookings
     * @param  \Carbon\Carbon  $date
     * @return array
     */
    private function calculateAvailableSlots($scheduleStart, $scheduleEnd, $durationMinutes, $existingBookings, $date)
    {
        $slots = [];
        $slotInterval = 30; // Check every 30 minutes

        // Parse schedule times
        $start = Carbon::parse($scheduleStart);
        $end = Carbon::parse($scheduleEnd);
        $now = Carbon::now();

        // If booking for today, don't show past times
        if ($date->isToday()) {
            $minTime = $now->copy()->addHours(1)->setMinutes(0); // At least 1 hour from now
            if ($minTime->greaterThan($start)) {
                $start = $minTime;
            }
        }

        $current = $start->copy();

        while ($current->copy()->addMinutes($durationMinutes)->lessThanOrEqualTo($end)) {
            $slotStart = $current->format('H:i:s');
            $slotEnd = $current->copy()->addMinutes($durationMinutes)->format('H:i:s');

            // Check if this slot conflicts with existing bookings
            $hasConflict = false;
            foreach ($existingBookings as $booking) {
                if ($this->timeSlotsOverlap($slotStart, $slotEnd, $booking->start_time, $booking->end_time)) {
                    $hasConflict = true;
                    break;
                }
            }

            if (!$hasConflict) {
                $slots[] = [
                    'start_time' => substr($slotStart, 0, 5), // HH:MM format
                    'end_time' => substr($slotEnd, 0, 5),
                    'display' => substr($slotStart, 0, 5) . ' - ' . substr($slotEnd, 0, 5),
                ];
            }

            $current->addMinutes($slotInterval);
        }

        return $slots;
    }

    /**
     * Check if two time slots overlap.
     *
     * @param  string  $start1
     * @param  string  $end1
     * @param  string  $start2
     * @param  string  $end2
     * @return bool
     */
    private function timeSlotsOverlap($start1, $end1, $start2, $end2)
    {
        $start1 = Carbon::parse($start1);
        $end1 = Carbon::parse($end1);
        $start2 = Carbon::parse($start2);
        $end2 = Carbon::parse($end2);

        return $start1->lessThan($end2) && $end1->greaterThan($start2);
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param  \App\Http\Requests\Booking\StoreBookingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create([
            'customer_id' => auth()->id(),
            'service_id' => $request->service_id,
            'stylist_id' => $request->stylist_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => Booking::STATUS_PENDING,
            'notes' => $request->notes,
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('status', 'Booking berhasil dibuat. Menunggu konfirmasi dari admin.');
    }

    /**
     * Display the specified booking.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        // Ensure customer can only view their own bookings
        if ($booking->customer_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $booking->load(['service', 'stylist.user', 'customer']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel the specified booking.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function cancel(Booking $booking)
    {
        // Ensure customer can only cancel their own bookings
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        // Check if booking can be cancelled
        if (!$booking->canBeCancelled()) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Booking ini tidak dapat dibatalkan.');
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return redirect()->route('bookings.index')
            ->with('status', 'Booking berhasil dibatalkan.');
    }
}
