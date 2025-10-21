<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingDetailRequest;
use App\Services\BookingDetail\IBookingDetailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingDetailController extends Controller
{
    protected $bookingDetailService;

    public function __construct(IBookingDetailService $bookingDetailService)
    {
        $this->bookingDetailService = $bookingDetailService;
    }

    public function createBookingDetail(BookingDetailRequest $request)
    {
        try {
            $bookingDetail = $this->bookingDetailService->createBookingDetail($request);
            return response()->json($bookingDetail, 200);
        } catch (Exception $e) {
            Log::error("Lỗi tạo booking detail: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getBookingDetailsByBookingId(int $bookingId)
    {
        try {
            $details = $this->bookingDetailService->getBookingDetailByBookingId($bookingId);
            return response()->json($details, 200);
        } catch (Exception $e) {
            Log::error("Lỗi lấy booking detail: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
