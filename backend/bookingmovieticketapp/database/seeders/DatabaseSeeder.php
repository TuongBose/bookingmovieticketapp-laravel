<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Booking\BookingService;
use App\Services\Movie\MovieService;
use App\Services\Room\RoomService;
use App\Services\Seat\SeatService;
use App\Services\ShowTime\ShowTimeService;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    // }

    public function run(): void
    {
        app(MovieService::class)->onInit();
        app(RoomService::class)->generateRoomsForAllCinemas();
        app(SeatService::class)->generateSeatsForAllRooms();
        app(ShowTimeService::class)->generateShowtimesForAllRooms();
        app(BookingService::class)->updateBookingStatus();
    }
}
