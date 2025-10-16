<?php

namespace App\Repositories\BookingDetail;

use App\Models\BookingDetail;

class BookingDetailRepository implements IBookingDetailRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByBookingId(int $bookingId)
    {
        return BookingDetail::where('bookingid', $bookingId)->get();
    }
}
