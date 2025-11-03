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
});
