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
        if (auth()->user()->isAdmin() || auth()->user()->isStylist()) {
            return redirect()->route('admin.bookings.index');
        }
        if (auth()->user()->isCustomer()) {
            return redirect()->route('bookings.index');
        }
    }
    return redirect('/login');
});

Route::get('/home', 'HomeController@index')->name('home');

// Admin only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', 'UserController')->except(['show']);
    Route::resource('services', 'ServiceController')->except(['show']);
    Route::resource('stylists', 'StylistController')->except(['show']);
    Route::resource('schedules', 'ScheduleController')->except(['show']);
});

// Admin & Stylist routes for booking management
Route::middleware(['auth', 'role:admin,stylist'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/bookings', 'Admin\BookingManagementController@index')->name('bookings.index');
    Route::patch('/bookings/{booking}/confirm', 'Admin\BookingManagementController@confirm')->name('bookings.confirm');
    Route::patch('/bookings/{booking}/complete', 'Admin\BookingManagementController@complete')->name('bookings.complete');
    Route::patch('/bookings/{booking}/cancel', 'Admin\BookingManagementController@cancel')->name('bookings.cancel');
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
