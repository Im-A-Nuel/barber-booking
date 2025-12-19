<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes (tidak perlu autentikasi)
Route::prefix('v1')->group(function () {
    // Services
    Route::get('/services', 'Api\ApiController@getServices');
    Route::get('/services/{id}', 'Api\ApiController@getServiceById');

    // Stylists
    Route::get('/stylists', 'Api\ApiController@getStylists');
    Route::get('/stylists/{id}', 'Api\ApiController@getStylistById');
});

// Protected API routes (perlu autentikasi)
Route::prefix('v1')->middleware('auth:api')->group(function () {
    // Bookings
    Route::get('/bookings', 'Api\ApiController@getBookings');
    Route::get('/bookings/{id}', 'Api\ApiController@getBookingById');
});
