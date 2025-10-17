<?php

namespace App\Services\BookingDetail;

use App\Http\Requests\BookingDetailRequest;

interface IBookingDetailService
{
    public function createBookingDetail(BookingDetailRequest $bookingDetailRequest);
    public function getBookingDetailById(int $id);
    public function updateBookingDetail(int $id, BookingDetailRequest $bookingDetailRequest);
    public function getBookingDetailByBookingId(int $bookingId);
}
