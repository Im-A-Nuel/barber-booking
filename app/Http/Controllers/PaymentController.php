<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the payments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Payment::query();

        if ($user->isAdmin()) {
            // Admin can see all payments
            $query->with(['booking.customer', 'booking.service', 'booking.stylist.user']);
        } else {
            // Customer can only see their own payments
            $query->with(['booking.service', 'booking.stylist.user'])
                ->whereHas('booking', function ($q) use ($user) {
                    $q->where('customer_id', $user->id);
                });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Append query params to pagination links
        $payments->appends($request->all());

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     *
     * @param  int  $bookingId
     * @return \Illuminate\Http\Response
     */
    public function create($bookingId)
    {
        $booking = Booking::with(['service', 'stylist.user', 'payment'])->findOrFail($bookingId);

        // Check authorization
        if ($booking->customer_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Check if payment already exists
        if ($booking->payment) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Pembayaran untuk booking ini sudah ada.');
        }

        // Only completed or confirmed bookings can be paid
        if (!in_array($booking->status, [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Hanya booking yang sudah dikonfirmasi atau selesai yang dapat dibayar.');
        }

        return view('payments.create', compact('booking'));
    }

    /**
     * Store a newly created payment in storage.
     *
     * @param  \App\Http\Requests\StorePaymentRequest  $request
     * @param  int  $bookingId
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request, $bookingId)
    {
        $booking = Booking::with(['service', 'payment'])->findOrFail($bookingId);

        // Check authorization
        if ($booking->customer_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Check if payment already exists
        if ($booking->payment) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Pembayaran untuk booking ini sudah ada.');
        }

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Create payment
            $payment = new Payment();
            $payment->booking_id = $booking->id;
            $payment->amount = $validated['amount'];
            $payment->method = $validated['method'];
            $payment->status = $validated['status'];

            // Set paid_at if status is paid
            if ($validated['status'] === 'paid') {
                $payment->paid_at = now();
            }

            $payment->save();

            // Auto-update booking status to completed if payment is paid
            if ($validated['status'] === 'paid' && $booking->status === Booking::STATUS_CONFIRMED) {
                $booking->status = Booking::STATUS_COMPLETED;
                $booking->save();
            }

            DB::commit();

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Pembayaran berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing payment status.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        $payment->load(['booking.customer', 'booking.service', 'booking.stylist.user']);

        // Check authorization
        if ($payment->booking->customer_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update payment status.
     *
     * @param  \App\Http\Requests\UpdatePaymentRequest  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->load('booking');

        // Check authorization
        if ($payment->booking->customer_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Store old status before updating
            $oldStatus = $payment->status;

            // Update payment status
            $payment->status = $validated['status'];

            // Update method if provided
            if ($request->filled('method')) {
                $payment->method = $validated['method'];
            }

            // Set paid_at if status changed to paid
            if ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
                $payment->paid_at = now();
            }

            // Clear paid_at if status changed from paid to pending/failed
            if ($validated['status'] !== 'paid' && $oldStatus === 'paid') {
                $payment->paid_at = null;
            }

            $payment->save();

            // Auto-update booking status to completed if payment is paid and booking is confirmed
            if ($validated['status'] === 'paid' && $payment->booking->status === Booking::STATUS_CONFIRMED) {
                $payment->booking->status = Booking::STATUS_COMPLETED;
                $payment->booking->save();
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Status pembayaran berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display payment receipt/invoice.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function showReceipt(Payment $payment)
    {
        $payment->load(['booking.customer', 'booking.service', 'booking.stylist.user']);

        // Check authorization
        if ($payment->booking->customer_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('payments.receipt', compact('payment'));
    }
}
