<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    BookingController,
    BookingDetailController,
    CastController,
    CinemaController,
    MovieController,
    RoomController,
    SeatController,
    ShowTimeController,
    StatisticController,
    UserController
};

Route::prefix('v1/bookings')->group(function () {
    Route::post('/', [BookingController::class, 'createBooking']);
    Route::get('/users/{id}/totalprice', [BookingController::class, 'sumTotalPriceByUserId']);
    Route::get('/showtimes/{showtimeId}/bookings', [BookingController::class, 'getBookingsByShowtimeId']);
    Route::get('/users/{userId}/bookings', [BookingController::class, 'getBookingByUserId']);
    Route::get('/', [BookingController::class, 'getAllBooking']);
});

Route::prefix('v1/bookingdetails')->group(function () {
    Route::post('/', [BookingDetailController::class, 'createBookingDetail']);
    Route::get('/{bookingId}/details', [BookingDetailController::class, 'getBookingDetailsByBookingId']);
});

Route::prefix('v1/casts')->group(function () {
    Route::get('/{id}', [CastController::class, 'getCastByMovieId']);
});

Route::prefix('v1/cinemas')->group(function () {
    Route::get('/movieandcityanddate', [CinemaController::class, 'getCinemaByMovieIdAndCityAndDate']);
    Route::get('/', [CinemaController::class, 'getAllCinema']);
    Route::get('/{id}', [CinemaController::class, 'getCinemaById']);

    Route::post('/', [CinemaController::class, 'createCinema']);
    Route::post('/{id}', [CinemaController::class, 'updateCinema']);
    Route::put('/{id}/status', [CinemaController::class, 'updateCinemaStatus']);

    Route::post('/{id}/image', [CinemaController::class, 'uploadCinemaImage']);
    Route::get('/{id}/image', [CinemaController::class, 'getCinemaImage']);
});

Route::prefix('v1/movies')->group(function () {
    Route::get('/nowplaying', [MovieController::class, 'getNowPlaying']);
    Route::get('/upcoming', [MovieController::class, 'getUpComing']);
    // Route::get('/similar/{movieId}', [MovieController::class, 'getSimilarMovies']);
    Route::get('/{id}', [MovieController::class, 'getMovieById']);
    Route::get('/', [MovieController::class, 'getAllMovie']);
    Route::get('/{id}/trailer', [MovieController::class, 'getTrailer'])->name('movies.trailer');

});

Route::prefix('v1/rooms')->group(function () {
    Route::get('/', [RoomController::class, 'getAllRooms']);
    Route::get('/{id}', [RoomController::class, 'getRoomById']);
    Route::get('/{roomId}/seats', [RoomController::class, 'getSeatsByRoomId']);
    Route::post('/', [RoomController::class, 'createRoom']);
    Route::put('/{id}', [RoomController::class, 'updateRoom']);
    Route::delete('/{id}', [RoomController::class, 'deleteRoom']);
    Route::get('/cinema/{cinemaId}', [RoomController::class, 'getRoomsByCinemaId']);
});

Route::prefix('v1/showtimes')->group(function () {
    Route::get('/', [ShowTimeController::class, 'getShowTimeByMovieIdAndCinemaIdAndDate']); // /v1/showtimes?movieId=&cinemaId=&date=
    Route::get('/cinemaanddate', [ShowTimeController::class, 'getShowtimesByCinemaAndDate']); // /v1/showtimes/cinemaanddate?cinemaId=&date=
    Route::get('/{id}', [ShowTimeController::class, 'getShowTimeById']); // /v1/showtimes/1
    Route::put('/{id}/status', [ShowTimeController::class, 'updateShowTimeStatus']); // /v1/showtimes/1/status
    Route::get('/{id}/bookings-count', [ShowTimeController::class, 'getBookingsCountForShowTime']); // /v1/showtimes/1/bookings-count
    Route::post('/', [ShowTimeController::class, 'createShowtime']); // POST /v1/showtimes
});

Route::prefix('v1/users')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login/customer', [UserController::class, 'loginCustomer']);
    Route::post('/login/admin', [UserController::class, 'loginAdmin']);
    Route::get('/admin', [UserController::class, 'getAllUserAdmin']);
    Route::get('/customer', [UserController::class, 'getAllUserCustomer']);
    Route::put('/{id}/status', [UserController::class, 'updateUserStatus']);
    Route::post('/{id}/image', [UserController::class, 'uploadUserImage']);
    Route::get('/{id}/image', [UserController::class, 'getUserImage']);
    Route::get('/{id}', [UserController::class, 'getUserById']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::get('/checkexistphonenumber/{phonenumber}', [UserController::class, 'checkExistsByphonenumber']);
    Route::get('/checkdoesnotexistphonenumber/{phonenumber}', [UserController::class, 'checkDoesNotExistsByphonenumber']);
    Route::post('/resetpassword', [UserController::class, 'resetPassword']);
});

Route::prefix('v1/seats')->group(function () {
    Route::get('/', [SeatController::class, 'getSeatByRoomId']);
    Route::get('/{id}', [SeatController::class, 'getSeatById']);
    Route::get('/seats/room/{roomId}/seat/{seatnumber}', [SeatController::class, 'getByRoomAndNumber']);
});

Route::prefix('v1/statistics')->group(function () {
    Route::get('/', [StatisticController::class, 'getMonthlyStatistics']);
});


