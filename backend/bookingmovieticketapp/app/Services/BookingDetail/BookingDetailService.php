<?php

namespace App\Services\BookingDetail;

use App\Http\Requests\BookingDetailRequest;
use App\Http\Resources\BookingDetailResource;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Seat;
use App\Repositories\Booking\IBookingRepository;
use App\Repositories\BookingDetail\IBookingDetailRepository;
use App\Repositories\Seat\ISeatRepository;

class BookingDetailService implements IBookingDetailService
{
    protected $bookingDetailRepository;
    protected $bookingRepository;
    protected $seatRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        IBookingDetailRepository $bookingDetailRepository,
        IBookingRepository $bookingRepository,
        ISeatRepository $seatRepository
    ) {
        $this->bookingDetailRepository = $bookingDetailRepository;
        $this->bookingRepository = $bookingRepository;
        $this->seatRepository = $seatRepository;
    }

    public function createBookingDetail(BookingDetailRequest $bookingDetailRequest)
    {
        $seat = Seat::findOrFail($bookingDetailRequest->seatid);
        $booking = Booking::findOrFail($bookingDetailRequest->bookingid);
        return BookingDetail::create([
            'bookingid' => $booking->id,
            'seatid' => $seat->id,
            'price' => $bookingDetailRequest->price,
        ]);
    }

    public function getBookingDetailById(int $id)
    {
        return BookingDetail::findOrFail($id);
    }

    public function updateBookingDetail(int $id, BookingDetailRequest $bookingDetailRequest)
    {
        $bookingDetail = BookingDetail::findOrFail($id);
        $seat = Seat::findOrFail($bookingDetailRequest->seatid);
        $booking = Booking::findOrFail($bookingDetailRequest->bookingid);

        $bookingDetail->update([
            'bookingid' => $booking->id,
            'seatid' => $seat->id,
            'price' => $bookingDetailRequest->price,
        ]);
        return $bookingDetail;
    }

    public function getBookingDetailByBookingId(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $bookingDetails = BookingDetail::where('id', $booking->id)->get();
        return BookingDetailResource::collection($bookingDetails);
    }
}
