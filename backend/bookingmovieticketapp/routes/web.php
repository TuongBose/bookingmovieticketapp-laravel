<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// Route::get('/', function () {
//     return view('welcome');
// });
//Auth Routes for Login, Register, and Password Reset --Kdan
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('checkout');
Route::get('/auth', [AuthController::class, 'showAuthPage'])->name('auth');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkByPhone'])->name('password.phone');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//--------------------------------------------------------------------------------------------------------------------


Route::get('api/v1/auth/check', function () {
    if (Auth::check()) {
        return response()->json([
            'authenticated' => true,
            'user' => Auth::user()
        ]);
    }
    return response()->json(['authenticated' => false]);
});

Route::post('/api/v1/checkout/prepare', [PaymentController::class, 'prepareCheckout']);
Route::post('/checkout/confirm', [PaymentController::class, 'confirmPayment'])
    ->name('checkout.confirm')
    ->middleware('auth');

Route::get('/booking-success', function () {
    if (!session('success_data')) {
        return redirect('/')->with('error', 'Không có thông tin vé');
    }
    return view('booking-success');
})->name('booking.success');

Route::get('/vnpay/callback', [PaymentController::class, 'vnpayCallback'])->name('vnpay.callback');

Route::get('/search', [MovieController::class, 'searchPage'])->name('search.page');



Route::get('/profile', [ProfileController::class, 'index'])
    ->name('profile')
    ->middleware('auth'); 

Route::post('/profile/update', [ProfileController::class, 'update'])
    ->name('profile.update')
    ->middleware('auth');

Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])
    ->name('profile.avatar')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});
Route::get('/{id}', [MovieController::class, 'moviedetail'])->name('movies.moviedetail')->where('id', '[1-9][0-9]*');
