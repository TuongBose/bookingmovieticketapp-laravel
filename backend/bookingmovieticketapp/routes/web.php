<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api/v1/bookings')->group(function () {
    Route::post('/', [BookingController::class, 'createBooking']);
    Route::get('/users/{id}/totalprice', [BookingController::class, 'sumTotalPriceByUserId']);
    Route::get('/showtimes/{showtimeId}/bookings', [BookingController::class, 'getBookingsByShowtimeId']);
    Route::get('/users/{userId}/bookings', [BookingController::class, 'getBookingByUserId']);
    Route::get('/', [BookingController::class, 'getAllBooking']);
});
