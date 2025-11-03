<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Stylist;
use App\Http\Requests\Schedule\StoreScheduleRequest;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stylistId = $request->get('stylist_id');
        $query = Schedule::with('stylist.user')->orderBy('stylist_id')->orderBy('day_of_week');

        if (!empty($stylistId)) {
            $query->where('stylist_id', $stylistId);
        }

        $schedules = $query->paginate(15)->appends($request->only('stylist_id'));
        $stylists = Stylist::with('user')->where('is_active', true)->orderBy('id')->get();

        return view('schedules.index', compact('schedules', 'stylists', 'stylistId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stylists = Stylist::with('user')->where('is_active', true)->orderBy('id')->get();
        $dayOptions = Schedule::getDayOptions();

        return view('schedules.create', compact('stylists', 'dayOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Schedule\StoreScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScheduleRequest $request)
    {
        Schedule::create($request->validated());

        return redirect()->route('schedules.index')
            ->with('status', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        $dayOptions = Schedule::getDayOptions();

        return view('schedules.edit', compact('schedule', 'dayOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Schedule\StoreScheduleRequest  $request
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(StoreScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());

        return redirect()->route('schedules.index')
            ->with('status', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('status', 'Jadwal berhasil dihapus.');
    }
}
