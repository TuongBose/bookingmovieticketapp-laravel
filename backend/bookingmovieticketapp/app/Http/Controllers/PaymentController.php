<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingDetailRequest;
use App\Http\Requests\BookingRequest;
use App\Models\Cinema;
use App\Models\User;
use App\Services\Booking\IBookingService;
use App\Services\BookingDetail\IBookingDetailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $bookingService;
    protected $bookingDetailService;
    public function __construct(
        IBookingService $bookingService,
        IBookingDetailService $bookingDetailService
    ) {
        $this->bookingService = $bookingService;
        $this->bookingDetailService = $bookingDetailService;
    }

    public function prepareCheckout(Request $request)
    {
        Log::info($request);
        $data = $request->validate([
            'movieId' => 'required|exists:movies,id',
            'movieName' => 'required|string',
            'ageRating' => 'required|string',
            'showtimeId' => 'required|exists:showtimes,id',
            'cinemaId' => 'required|exists:cinemas,id',
            'cinemaName' => 'required|string',
            'roomName' => 'required|string',
            'showtime' => 'required|string',
            'seats' => 'required|array|min:1',
            'seats.*' => 'string',
            'seatIds' => 'required|array|min:1',
            'seatIds.*' => 'integer|exists:seats,id',
            'pricePerSeat' => 'required|numeric',
            'totalPrice' => 'required|numeric',
            'userId' => 'required|exists:users,id',
        ]);

        $user = User::find($data['userId']);
        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 401);
        }

        $cinema = Cinema::find($data['cinemaId']);
        if (!$cinema) {
            return response()->json(['message' => 'Rạp không tồn tại'], 404);
        }

        $data['cinemaName'] = $cinema->name . ' (' . $cinema->city . ')';

        // Lưu tạm vào session
        session([
            'checkout_data' => $data
        ]);

        return response()->json(['message' => 'Chuẩn bị thanh toán thành công', 'redirect' => '/checkout']);
    }

    public function showCheckout()
    {
        $data = session('checkout_data');

        if (!$data) {
            return redirect('/')->with('error', 'Không tìm thấy thông tin đặt vé. Vui lòng chọn lại.');
        }

        return view('checkout', compact('data'));
    }

    public function confirmPayment(Request $request)
    {
        $data = session('checkout_data');
        if (!$data) {
            return response()->json(['error' => 'Dữ liệu đặt vé không tồn tại'], 400);
        }

        // === 1. LẤY USER ĐÃ ĐĂNG NHẬP ===
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
        }
        $userId = Auth::id();

        // === 2. TẠO BOOKING ===
        $bookingData = [
            'userid' => $userId,
            'showtimeid' => $data['showtimeId'],
            'totalprice' => (int) $data['totalPrice'],
            'paymentmethod' => 'momo',
            'paymentstatus' => 'ok'
        ];

        try {
            // Tạo BookingRequest từ dữ liệu
            $bookingRequest = new BookingRequest($bookingData);
            $bookingRequest->setMethod('POST');

            // Validate
            $validator = validator($bookingRequest->all(), $bookingRequest->rules(), $bookingRequest->messages());
            if ($validator->fails()) {
                throw new Exception('Dữ liệu booking không hợp lệ: ' . implode(', ', $validator->errors()->all()));
            }

            // Gọi Service
            $booking = $this->bookingService->createBooking($bookingRequest);

            // Kiểm tra kết quả
            if (!$booking || !isset($booking->id)) {
                throw new Exception('Tạo booking thất bại: Không nhận được ID');
            }

            $bookingId = $booking->id;

            // === 3. TẠO BOOKING DETAIL CHO TỪNG GHẾ ===
            foreach ($data['seatIds'] as $index => $seatId) {
                $detailData = [
                    'bookingid' => $bookingId,
                    'seatid' => $seatId, 
                    'price' => (int) $data['pricePerSeat']
                ];

                $detailRequest = new BookingDetailRequest($detailData);
                $detailRequest->setMethod('POST');

                $validator = validator($detailRequest->all(), $detailRequest->rules(), $detailRequest->messages());
                if ($validator->fails()) {
                    Log::warning("Validate ghế $seatId thất bại", $validator->errors()->toArray());
                    continue;
                }

                $detail = $this->bookingDetailService->createBookingDetail($detailRequest);
                if (!$detail) {
                    Log::error("Tạo booking detail thất bại cho ghế: $seatId");
                }
            }

            // === 5. XÓA SESSION ===
            session()->forget('checkout_data');

            // === 6. CHUYỂN HƯỚNG THÀNH CÔNG ===
            return redirect('/booking-success')
                ->with('success_data', [
                    'bookingId' => $bookingId,
                    'movieName' => $data['movieName'],
                    'cinemaName' => $data['cinemaName'],
                    'roomName' => $data['roomName'],
                    'showtime' => $data['showtime'],
                    'seats' => $data['seats'],
                    'totalPrice' => $data['totalPrice']
                ]);

        } catch (Exception $e) {
            Log::error('Checkout confirm failed: ' . $e->getMessage());
            return response()->json(['error' => 'Thanh toán thất bại. Vui lòng thử lại.'], 500);
        }
    }
}