<?php


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

// Payment routes (Customer & Admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/payments', 'PaymentController@index')->name('payments.index');
    Route::get('/payments/{booking}/create', 'PaymentController@create')->name('payments.create');
    Route::post('/payments/{booking}', 'PaymentController@store')->name('payments.store');
    Route::get('/payments/{payment}/edit', 'PaymentController@edit')->name('payments.edit');
    Route::put('/payments/{payment}', 'PaymentController@update')->name('payments.update');
    Route::get('/payments/{payment}/receipt', 'PaymentController@showReceipt')->name('payments.receipt');

    // Payment Gateway routes
    Route::get('/payments/{booking}/gateway', 'PaymentController@createWithGateway')->name('payments.gateway');
    Route::get('/payments/{payment}/check-status', 'PaymentController@checkStatus')->name('payments.check-status');
    Route::post('/payments/{payment}/simulate-success', 'PaymentController@simulateSuccess')->name('payments.simulate-success');
});

// Midtrans callback (no auth middleware needed for webhook)
Route::post('/payments/midtrans/callback', 'PaymentController@midtransCallback')->name('payments.midtrans.callback');
