<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/services');
    }
    return redirect('/login');
});

Route::get('/home', 'HomeController@index')->name('home');

// Admin only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('services', 'ServiceController')->except(['show']);
    Route::resource('stylists', 'StylistController')->except(['show']);
    Route::resource('schedules', 'ScheduleController')->except(['show']);
});

// Customer only routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/bookings', 'BookingController@index')->name('bookings.index');
    Route::get('/bookings/create', 'BookingController@create')->name('bookings.create');
    Route::get('/bookings/select-stylist', 'BookingController@selectStylist')->name('bookings.select-stylist');
    Route::get('/bookings/select-datetime', 'BookingController@selectDateTime')->name('bookings.select-datetime');
    Route::get('/bookings/available-slots', 'BookingController@getAvailableSlots')->name('bookings.available-slots');
    Route::post('/bookings', 'BookingController@store')->name('bookings.store');
    Route::get('/bookings/{booking}', 'BookingController@show')->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', 'BookingController@cancel')->name('bookings.cancel');
});
