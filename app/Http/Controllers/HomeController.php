<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Redirect based on user role
        if (auth()->user()->isAdmin() || auth()->user()->isStylist()) {
            return redirect()->route('admin.bookings.index');
        }

        if (auth()->user()->isCustomer()) {
            return redirect()->route('bookings.index');
        }

        return view('home');
    }
}
