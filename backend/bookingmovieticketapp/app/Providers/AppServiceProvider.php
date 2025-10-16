<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // BookingDetail
        $this->app->bind(
            \App\Repositories\BookingDetail\IBookingDetailRepository::class,
            \App\Repositories\BookingDetail\BookingDetailRepository::class
        );

        // Booking
        $this->app->bind(
            \App\Repositories\Booking\IBookingRepository::class,
            \App\Repositories\Booking\BookingRepository::class
        );

        // Cast
        $this->app->bind(
            \App\Repositories\Cast\ICastRepository::class,
            \App\Repositories\Cast\CastRepository::class
        );

        // Cinema
        $this->app->bind(
            \App\Repositories\Cinema\ICinemaRepository::class,
            \App\Repositories\Cinema\CinemaRepository::class
        );

        // Movie
        $this->app->bind(
            \App\Repositories\Movie\IMovieRepository::class,
            \App\Repositories\Movie\MovieRepository::class
        );

        // Room
        $this->app->bind(
            \App\Repositories\Room\IRoomRepository::class,
            \App\Repositories\Room\RoomRepository::class
        );

        // Seat
        $this->app->bind(
            \App\Repositories\Seat\ISeatRepository::class,
            \App\Repositories\Seat\SeatRepository::class
        );

        // ShowTime
        $this->app->bind(
            \App\Repositories\ShowTime\IShowTimeRepository::class,
            \App\Repositories\ShowTime\ShowTimeRepository::class
        );

        // User
        $this->app->bind(
            \App\Repositories\User\IUserRepository::class,
            \App\Repositories\User\UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
