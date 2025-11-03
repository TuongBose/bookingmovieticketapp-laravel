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
            'posterurl' => 'required|string',
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

        $paymentMethod = $request->input('payment_method');
        if (!in_array($paymentMethod, ['momo', 'vnpay', 'shopeepay', 'bank_transfer'])) {
            return redirect()->back()->withErrors(['payment_method' => 'Phương thức không hợp lệ']);
        }

        // === 1. LẤY USER ĐÃ ĐĂNG NHẬP ===
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
        }
        $userId = Auth::id();

        if ($paymentMethod === 'vnpay') {
            $vnpUrl = $this->generateVNPayUrl($data, $userId);
            return redirect($vnpUrl);
        }
        return $this->processDirectPayment($data, $userId, $paymentMethod);
    }

    private function processDirectPayment($data, $userId, $paymentMethod)
    {
        $bookingData = [
            'userid' => $userId,
            'showtimeid' => $data['showtimeId'],
            'totalprice' => (int) $data['totalPrice'],
            'paymentmethod' => $paymentMethod,
            'paymentstatus' => 'ok'
        ];

        try {
            $bookingRequest = new BookingRequest($bookingData);
            $bookingRequest->setMethod('POST');
            $validator = validator($bookingRequest->all(), $bookingRequest->rules());
            if ($validator->fails())
                throw new Exception('Dữ liệu không hợp lệ');

            $booking = $this->bookingService->createBooking($bookingRequest);
            if (!$booking || !isset($booking->id))
                throw new Exception('Tạo booking thất bại');

            $bookingId = $booking->id;

            foreach ($data['seatIds'] as $seatId) {
                $detailData = ['bookingid' => $bookingId, 'seatid' => $seatId, 'price' => (int) $data['pricePerSeat']];
                $detailRequest = new BookingDetailRequest($detailData);
                $detailRequest->setMethod('POST');
                $this->bookingDetailService->createBookingDetail($detailRequest);
            }

            session()->forget('checkout_data');

            return redirect('/booking-success')->with('success_data', [
                'bookingId' => $bookingId,
                'movieName' => $data['movieName'],
                'posterurl' => $data['posterurl'],
                'cinemaName' => $data['cinemaName'],
                'roomName' => $data['roomName'],
                'showtime' => $data['showtime'],
                'seats' => $data['seats'],
                'totalPrice' => $data['totalPrice'],
                'paymentMethod' => ucfirst(str_replace('_', ' ', $paymentMethod))
            ]);

        } catch (Exception $e) {
            Log::error('Direct payment failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
        }
    }

    private function generateVNPayUrl($data, $userId)
    {
        $vnp_TmnCode = "OZYHVEZ5"; // Thay bằng của bạn
        $vnp_HashSecret = "O9G96CAW41KRP6YKLVNKA9W04L0B7XEN"; // Thay bằng của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.callback'); // Route xử lý callback
        $vnp_TxnRef = $userId . '_' . time();
        $vnp_OrderInfo = "Thanh toan ve phim: " . $data['movieName'];
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $data['totalPrice'] * 100;
        $vnp_Locale = "vn";
        $vnp_IpAddr = request()->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $hashdata = http_build_query($inputData, '', '&');
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $hashdata . '&vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    public function vnpayCallback(Request $request)
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->except('vnp_SecureHash');
        ksort($inputData);
        $hashdata = http_build_query($inputData, '', '&');
        $secureHash = hash_hmac('sha512', $hashdata, "O9G96CAW41KRP6YKLVNKA9W04L0B7XEN");

        if ($secureHash === $vnp_SecureHash && $request->vnp_ResponseCode == '00') {
            // Thành công → xử lý như direct payment
            [$userId, $timestamp] = explode('_', $request->vnp_TxnRef);
            $data = session('checkout_data');
            if ($data) {
                $this->processDirectPayment($data, $userId, 'vnpay');
            }
            return redirect('/booking-success');
        }

        return redirect('/checkout')->with('error', 'Thanh toán VNPay thất bại: ' . $request->vnp_ResponseCode);
    }
}