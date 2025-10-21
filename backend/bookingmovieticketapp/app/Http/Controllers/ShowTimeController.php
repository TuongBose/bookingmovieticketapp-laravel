<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowtimeRequest;
use App\Services\ShowTime\IShowTimeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShowTimeController extends Controller
{
    protected $showTimeService;

    public function __construct(IShowTimeService $showTimeService)
    {
        $this->showTimeService = $showTimeService;
    }

    public function getShowTimeByMovieIdAndCinemaIdAndDate(Request $request)
    {
        try {
            $movieId = $request->query('movieId');
            $cinemaId = $request->query('cinemaId');
            $date = Carbon::parse($request->query('date'));

            $showTimes = $this->showTimeService->getShowTimeByMovieIdAndCinemaIdAndDate($movieId, $cinemaId, $date);
            return response()->json($showTimes);
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy danh sách suất chiếu: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getShowtimesByCinemaAndDate(Request $request)
    {
        try {
            $cinemaId = $request->query('cinemaId');
            $date = Carbon::parse($request->query('date'));

            $showTimes = $this->showTimeService->getShowtimesByCinemaIdAndDate($cinemaId, $date);
            return response()->json($showTimes);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getShowTimeById($id)
    {
        try {
            return response()->json($this->showTimeService->getShowTimeById($id));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateShowTimeStatus($id, Request $request)
    {
        try {
            $isActive = $request->input('isActive');
            $this->showTimeService->updateShowTimeStatus($id, $isActive);
            return response()->json(['message' => 'Cập nhật trạng thái suất chiếu thành công.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getBookingsCountForShowTime($id)
    {
        try {
            $count = $this->showTimeService->getBookingsCountForShowTime($id);
            return response()->json(['bookingsCount' => $count]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createShowtime(Request $request)
    {
        try {
            $movieId = $request->input('movieid');
            $roomId = $request->input('roomid');
            $showDate = Carbon::parse($request->input('showdate'))->toDateString();
            $startTime = Carbon::parse($request->input('starttime'))->toDateTimeString();
            $price = $request->input('price');

            $showtimeData = new ShowtimeRequest([
                'movieid' => $movieId,
                'roomid' => $roomId,
                'showdate' => $showDate,
                'starttime' => $startTime,
                'price' => $price,
            ]);

            $newShowTime = $this->showTimeService->createShowTime($showtimeData);
            return response()->json($newShowTime);
        } catch (Exception $e) {
            Log::error("Lỗi khi tạo suất chiếu: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
