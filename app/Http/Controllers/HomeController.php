<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Buat instance baru controller ini.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan dashboard aplikasi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Redirect berdasarkan peran pengguna
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.bookings.index');
        }

        if (auth()->user()->isStylist()) {
            // Cek jika stylist memiliki profil sebelum mengalihkan
            if (auth()->user()->stylist) {
                return redirect()->route('admin.bookings.index');
            }
            // Jika tidak ada profil stylist, tetap di halaman beranda untuk menampilkan pesan kesalahan
        }

        if (auth()->user()->isCustomer()) {
            return redirect()->route('bookings.index');
        }

        return view('home');
    }
}
