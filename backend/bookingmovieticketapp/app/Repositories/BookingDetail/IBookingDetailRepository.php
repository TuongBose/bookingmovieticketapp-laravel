<?php

namespace App\Repositories\BookingDetail;

interface IBookingDetailRepository
{
    public function findByBookingId(int $bookingId);
}
