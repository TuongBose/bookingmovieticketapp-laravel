<?php

namespace App\Repositories\Booking;

use Carbon\Carbon;

interface IBookingRepository
{
    public function findByUserId(int $userId);

    public function findByShowTimeId(int $showTimeId);

    public function findMostBookedMovie(Carbon $startDate, Carbon $endDate);

    public function calculateTotalRevenue(Carbon $startDate, Carbon $endDate);

    public function findMostBookedCinema(Carbon $startDate, Carbon $endDate);

    public function findSecondMostBookedMovie(Carbon $startDate, Carbon $endDate);

    public function findThirdMostBookedMovie(Carbon $startDate, Carbon $endDate);
}
