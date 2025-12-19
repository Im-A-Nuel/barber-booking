<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Payment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show admin dashboard
     */
    public function admin()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isStylist()) {
            abort(403, 'Unauthorized action.');
        }

        // Stats untuk cards
        $todayBookings = Booking::whereDate('booking_date', today())->count();
        
        $thisWeekBookings = Booking::whereBetween('booking_date', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ])->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])->count();
        
        $thisWeekRevenue = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');
        
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Upcoming appointments
        $upcomingAppointments = Booking::with(['customer', 'service', 'stylist.user'])
            ->whereDate('booking_date', '>=', today())
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Payment methods distribution
        $paymentMethods = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [now()->subDays(7), now()])
            ->select('method', DB::raw('count(*) as count'))
            ->groupBy('method')
            ->get();

        // Calculate previous period stats for comparison
        $lastWeekBookings = Booking::whereBetween('booking_date', [
            now()->subWeek()->startOfWeek(), 
            now()->subWeek()->endOfWeek()
        ])->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])->count();
        
        $yesterdayBookings = Booking::whereDate('booking_date', today()->subDay())->count();
        
        $bookingTrend = $yesterdayBookings > 0 
            ? round((($todayBookings - $yesterdayBookings) / $yesterdayBookings) * 100) 
            : 0;
        
        $weeklyBookingTrend = $lastWeekBookings > 0 
            ? round((($thisWeekBookings - $lastWeekBookings) / $lastWeekBookings) * 100) 
            : 0;

        return view('dashboard.admin', compact(
            'todayBookings',
            'thisWeekBookings',
            'thisWeekRevenue',
            'pendingPayments',
            'upcomingAppointments',
            'paymentMethods',
            'bookingTrend',
            'weeklyBookingTrend'
        ));
    }

    /**
     * Show customer dashboard
     */
    public function customer()
    {
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();

        // Next upcoming appointment
        $nextAppointment = Booking::with(['service', 'stylist.user', 'payment'])
            ->where('customer_id', $user->id)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->whereDate('booking_date', '>=', today())
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->first();

        // Payment alerts (failed or pending)
        $paymentAlerts = Payment::whereHas('booking', function($query) use ($user) {
                $query->where('customer_id', $user->id);
            })
            ->whereIn('status', ['pending', 'failed'])
            ->with(['booking.service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Favorite services (most booked services)
        $favoriteServices = Booking::where('customer_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->select('service_id', DB::raw('count(*) as booking_count'))
            ->groupBy('service_id')
            ->orderBy('booking_count', 'desc')
            ->with('service')
            ->limit(2)
            ->get();

        // Booking history
        $bookingHistory = Booking::with(['service', 'stylist.user'])
            ->where('customer_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->orderBy('booking_date', 'desc')
            ->limit(3)
            ->get();

        return view('dashboard.customer', compact(
            'nextAppointment',
            'paymentAlerts',
            'favoriteServices',
            'bookingHistory'
        ));
    }
}
