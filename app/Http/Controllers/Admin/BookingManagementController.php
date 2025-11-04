<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    /**
     * Display a listing of bookings for admin/stylist.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'service', 'stylist.user'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');

        // If user is stylist, only show their bookings
        if (auth()->user()->isStylist()) {
            $stylist = auth()->user()->stylist;
            if (!$stylist) {
                abort(403, 'Stylist profile not found.');
            }
            $query->where('stylist_id', $stylist->id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Confirm a booking (change status from pending to confirmed).
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function confirm(Booking $booking)
    {
        // Authorization: admin can confirm any, stylist only their own
        if (auth()->user()->isStylist()) {
            $stylist = auth()->user()->stylist;
            if (!$stylist || $booking->stylist_id != $stylist->id) {
                abort(403, 'Unauthorized to confirm this booking.');
            }
        }

        // Only pending bookings can be confirmed
        if (!$booking->isPending()) {
            return redirect()->back()
                ->with('error', 'Hanya booking dengan status pending yang bisa dikonfirmasi.');
        }

        $booking->update(['status' => Booking::STATUS_CONFIRMED]);

        return redirect()->back()
            ->with('status', 'Booking berhasil dikonfirmasi.');
    }

    /**
     * Mark a booking as completed.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function complete(Booking $booking)
    {
        // Authorization: admin can complete any, stylist only their own
        if (auth()->user()->isStylist()) {
            $stylist = auth()->user()->stylist;
            if (!$stylist || $booking->stylist_id != $stylist->id) {
                abort(403, 'Unauthorized to complete this booking.');
            }
        }

        // Only confirmed bookings can be completed
        if (!$booking->isConfirmed()) {
            return redirect()->back()
                ->with('error', 'Hanya booking dengan status confirmed yang bisa diselesaikan.');
        }

        $booking->update(['status' => Booking::STATUS_COMPLETED]);

        return redirect()->back()
            ->with('status', 'Booking berhasil diselesaikan.');
    }

    /**
     * Cancel a booking.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function cancel(Booking $booking)
    {
        // Authorization: admin can cancel any, stylist only their own
        if (auth()->user()->isStylist()) {
            $stylist = auth()->user()->stylist;
            if (!$stylist || $booking->stylist_id != $stylist->id) {
                abort(403, 'Unauthorized to cancel this booking.');
            }
        }

        // Cannot cancel completed bookings
        if ($booking->status === Booking::STATUS_COMPLETED) {
            return redirect()->back()
                ->with('error', 'Booking yang sudah selesai tidak dapat dibatalkan.');
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return redirect()->back()
            ->with('status', 'Booking berhasil dibatalkan.');
    }
}
