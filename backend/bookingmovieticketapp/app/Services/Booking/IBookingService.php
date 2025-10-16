<?php

namespace App\Services\Booking;

use App\Http\Requests\BookingRequest;

interface IBookingService
{
    public function createBooking(BookingRequest $request);

    public function getBookingByShowTimeId(int $id);

    public function getBookingByUserId(int $id);

    public function getAllBooking();

    public function getBookingById(int $id);

    public function updateBooking(int $id, BookingRequest $request);

    public function deleteBooking(int $id);

    public function sumTotalPriceByUserId(int $userId);

    public function getMostBookedMovie(int $month, int $year);

    public function calculateMonthlyRevenue(int $month, int $year);

    public function getMostBookedCinema(int $month, int $year);

    public function getSecondMostBookedMovie(int $month, int $year);

    public function getThirdMostBookedMovie(int $month, int $year);
}
