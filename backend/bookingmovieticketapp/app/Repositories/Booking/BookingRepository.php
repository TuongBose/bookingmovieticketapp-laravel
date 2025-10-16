<?php

namespace App\Repositories\Booking;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingRepository implements IBookingRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByUserId(int $userId)
    {
        return DB::table('bookings')
            ->where('userid', $userId)
            ->get();
    }

    public function findByShowTimeId(int $showTimeId)
    {
        return DB::table('bookings')
            ->where('showtimeid', $showTimeId)
            ->get();
    }

    public function findMostBookedMovie(Carbon $startDate, Carbon $endDate)
    {
        return DB::select("
            SELECT m.id, m.name, COUNT(b.id) as total_bookings
            FROM movies m
            JOIN showtimes s ON s.movieid = m.id
            JOIN bookings b ON b.showtimeid = s.id
            WHERE b.bookingdate BETWEEN ? AND ?
            GROUP BY m.id, m.name
            ORDER BY total_bookings DESC
            LIMIT 1
        ", [$startDate, $endDate]);
    }

    public function calculateTotalRevenue(Carbon $startDate, Carbon $endDate)
    {
        $result = DB::selectOne("
            SELECT SUM(b.totalprice) as total_revenue
            FROM bookings b
            WHERE b.bookingdate BETWEEN ? AND ?
        ", [$startDate, $endDate]);

        return $result ? $result->total_revenue : 0;
    }

    public function findMostBookedCinema(Carbon $startDate, Carbon $endDate)
    {
        return DB::select("
            SELECT c.id, c.name, COUNT(b.id) as total_bookings
            FROM cinemas c
            JOIN rooms ch ON ch.cinemaid = c.id
            JOIN showtimes s ON s.roomid = ch.id
            JOIN bookings b ON b.showtimeid = s.id
            WHERE b.bookingdate BETWEEN ? AND ?
            AND s.starttime BETWEEN ? AND ?
            GROUP BY c.id, c.name
            ORDER BY total_bookings DESC
            LIMIT 1
        ", [$startDate, $endDate, $startDate, $endDate]);
    }

    public function findSecondMostBookedMovie(Carbon $startDate, Carbon $endDate)
    {
        return DB::select("
            SELECT m.id, m.name, COUNT(b.id) as total_bookings
            FROM movies m
            JOIN showtimes s ON s.movieid = m.id
            JOIN bookings b ON b.showtimeid = s.id
            WHERE b.bookingdate BETWEEN ? AND ?
            AND s.starttime BETWEEN ? AND ?
            GROUP BY m.id, m.name
            ORDER BY total_bookings DESC
            LIMIT 1 OFFSET 1
        ", [$startDate, $endDate, $startDate, $endDate]);
    }

    public function findThirdMostBookedMovie(Carbon $startDate, Carbon $endDate)
    {
        return DB::select("
            SELECT m.id, m.name, COUNT(b.id) as total_bookings
            FROM movies m
            JOIN showtimes s ON s.movieid = m.id
            JOIN bookings b ON b.showtimeid = s.id
            WHERE b.bookingdate BETWEEN ? AND ?
            AND s.starttime BETWEEN ? AND ?
            GROUP BY m.id, m.name
            ORDER BY total_bookings DESC
            LIMIT 1 OFFSET 2
        ", [$startDate, $endDate, $startDate, $endDate]);
    }
}
