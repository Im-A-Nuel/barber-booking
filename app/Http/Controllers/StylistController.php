<?php

namespace App\Http\Controllers;

use App\Stylist;
use App\User;
use App\Http\Requests\Stylist\StoreStylistRequest;
use Illuminate\Http\Request;

class StylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Stylist::with('user')->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhere('specialty', 'like', '%' . $search . '%');
        }

        $stylists = $query->paginate(10)->appends($request->only('search'));

        return view('stylists.index', compact('stylists', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get users with 'stylist' role that don't have a stylist profile yet
        $availableUsers = User::where('role', 'stylist')
            ->whereDoesntHave('stylist')
            ->orderBy('name')
            ->get();

        return view('stylists.create', compact('availableUsers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Stylist\StoreStylistRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStylistRequest $request)
    {
        Stylist::create($request->validated());

        return redirect()->route('stylists.index')
            ->with('status', 'Stylist berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stylist  $stylist
     * @return \Illuminate\Http\Response
     */
    public function edit(Stylist $stylist)
    {
        return view('stylists.edit', compact('stylist'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Stylist\StoreStylistRequest  $request
     * @param  \App\Stylist  $stylist
     * @return \Illuminate\Http\Response
     */
    public function update(StoreStylistRequest $request, Stylist $stylist)
    {
        $stylist->update($request->validated());

        return redirect()->route('stylists.index')
            ->with('status', 'Stylist berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stylist  $stylist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stylist $stylist)
    {
        $stylist->delete();

        return redirect()->route('stylists.index')
            ->with('status', 'Stylist berhasil dihapus.');
    }
}
