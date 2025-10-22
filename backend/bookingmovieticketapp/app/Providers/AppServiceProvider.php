<?php

namespace App\Providers;

use App\Repositories\Booking\BookingRepository;
use App\Repositories\Booking\IBookingRepository;
use App\Repositories\BookingDetail\BookingDetailRepository;
use App\Repositories\BookingDetail\IBookingDetailRepository;
use App\Repositories\Cast\CastRepository;
use App\Repositories\Cast\ICastRepository;
use App\Repositories\Cinema\CinemaRepository;
use App\Repositories\Cinema\ICinemaRepository;
use App\Repositories\Movie\IMovieRepository;
use App\Repositories\Movie\MovieRepository;
use App\Repositories\Room\IRoomRepository;
use App\Repositories\Room\RoomRepository;
use App\Repositories\Seat\ISeatRepository;
use App\Repositories\Seat\SeatRepository;
use App\Repositories\ShowTime\IShowTimeRepository;
use App\Repositories\ShowTime\ShowTimeRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\User\UserRepository;
use App\Services\Booking\BookingService;
use App\Services\Booking\IBookingService;
use App\Services\BookingDetail\BookingDetailService;
use App\Services\BookingDetail\IBookingDetailService;
use App\Services\Cast\CastService;
use App\Services\Cast\ICastService;
use App\Services\InitService;
use App\Services\Cinema\CinemaService;
use App\Services\Cinema\ICinemaService;
use App\Services\Movie\IMovieService;
use App\Services\Movie\MovieService;
use App\Services\Room\IRoomService;
use App\Services\Room\RoomService;
use App\Services\Seat\ISeatService;
use App\Services\Seat\SeatService;
use App\Services\ShowTime\IShowTimeService;
use App\Services\ShowTime\ShowTimeService;
use App\Services\User\IUserService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(IBookingDetailRepository::class, BookingDetailRepository::class);
        $this->app->bind(IBookingRepository::class, BookingRepository::class);
        $this->app->bind(ICastRepository::class, CastRepository::class);
        $this->app->bind(ICinemaRepository::class, CinemaRepository::class);
        $this->app->bind(IMovieRepository::class, MovieRepository::class);
        $this->app->bind(IRoomRepository::class, RoomRepository::class);
        $this->app->bind(ISeatRepository::class, SeatRepository::class);
        $this->app->bind(IShowTimeRepository::class, ShowTimeRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);

        // Services
        $this->app->bind(IBookingService::class, BookingService::class);
        $this->app->bind(IBookingDetailService::class, BookingDetailService::class);
        $this->app->bind(ICastService::class, CastService::class);
        $this->app->bind(ICinemaService::class, CinemaService::class);
        $this->app->bind(IMovieService::class, MovieService::class);
        $this->app->bind(IRoomService::class, RoomService::class);
        $this->app->bind(ISeatService::class, SeatService::class);
        $this->app->bind(IShowTimeService::class, ShowTimeService::class);
        $this->app->bind(IUserService::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MovieService $movieService): void
    {
        // try {
        //     Log::info('Khởi động MovieService onInit()...');
        //     $initService->onInit(); // gọi hàm onInit()
        // } catch (\Exception $e) {
        //     Log::error('Lỗi khi chạy onInit MovieService: ' . $e->getMessage());
        // }
    }
}
