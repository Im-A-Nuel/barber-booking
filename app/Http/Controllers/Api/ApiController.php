<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use App\Stylist;
use App\Booking;
use App\User;

class ApiController extends Controller
{
    /**
     * Mendapatkan semua services yang aktif
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServices(Request $request)
    {
        $query = Service::where('is_active', true);

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan harga
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $services = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);
    }

    /**
     * Mendapatkan detail service berdasarkan ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceById($id)
    {
        $service = Service::where('is_active', true)->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ], 200);
    }

    /**
     * Mendapatkan semua stylists yang aktif
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStylists()
    {
        $stylists = Stylist::with('user')
            ->where('is_active', true)
            ->get()
            ->map(function($stylist) {
                return [
                    'id' => $stylist->id,
                    'user_id' => $stylist->user_id,
                    'name' => $stylist->user->name,
                    'phone' => $stylist->phone,
                    'specialization' => $stylist->specialization,
                    'is_active' => $stylist->is_active,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $stylists
        ], 200);
    }

    /**
     * Mendapatkan detail stylist berdasarkan ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStylistById($id)
    {
        $stylist = Stylist::with('user')->where('is_active', true)->find($id);

        if (!$stylist) {
            return response()->json([
                'success' => false,
                'message' => 'Stylist tidak ditemukan'
            ], 404);
        }

        $data = [
            'id' => $stylist->id,
            'user_id' => $stylist->user_id,
            'name' => $stylist->user->name,
            'phone' => $stylist->phone,
            'specialization' => $stylist->specialization,
            'is_active' => $stylist->is_active,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Mendapatkan daftar booking (hanya untuk authenticated user)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookings(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $query = Booking::with(['service', 'stylist.user', 'customer']);

        // Jika user adalah customer, hanya tampilkan booking miliknya
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Jika user adalah stylist, hanya tampilkan booking untuk stylist tersebut
        if ($user->isStylist() && $user->stylist) {
            $query->where('stylist_id', $user->stylist->id);
        }

        // Admin bisa melihat semua booking

        $bookings = $query->orderBy('booking_date', 'desc')
                          ->orderBy('booking_time', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ], 200);
    }

    /**
     * Mendapatkan detail booking berdasarkan ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingById(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $booking = Booking::with(['service', 'stylist.user', 'customer'])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        // Cek authorization
        if ($user->isCustomer() && $booking->customer_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden'
            ], 403);
        }

        if ($user->isStylist() && $user->stylist && $booking->stylist_id != $user->stylist->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ], 200);
    }
}
