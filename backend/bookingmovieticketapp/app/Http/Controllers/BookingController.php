<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Services\Booking\IBookingService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(IBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function createBooking(BookingRequest $bookingRequest)
    {
        try {
            $booking = $this->bookingService->createBooking($bookingRequest);
            return response()->json($booking, 200);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 400);
        } catch (Exception $e) {
            Log::error('Lỗi khi tạo booking: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sumTotalPriceByUserId(int $id)
    {
        try {
            $totalPrice = $this->bookingService->sumTotalPriceByUserId($id);
            return response()->json($totalPrice, 200);
        } catch (Exception $e) {
            Log::error('Lỗi khi tính tổng tiền của user: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getBookingsByShowtimeId(int $showtimeId)
    {
        try {
            $bookings = $this->bookingService->getBookingByShowTimeId($showtimeId);
            return response()->json($bookings, 200);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy booking theo showtimeId: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getBookingByUserId(int $userId)
    {
        try {
            $bookings = $this->bookingService->getBookingByUserId($userId);
            return response()->json($bookings, 200);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy booking theo userId: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllBooking()
    {
        try {
            $bookings = $this->bookingService->getAllBooking();
            return response()->json($bookings, 200);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy danh sách booking: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
