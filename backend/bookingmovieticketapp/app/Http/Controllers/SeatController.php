<?php

namespace App\Http\Controllers;

use App\Services\Seat\ISeatService;
use Exception;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    protected $seatService;

    public function __construct(ISeatService $seatService)
    {
        $this->seatService = $seatService;
    }

    public function getSeatByRoomId(Request $request)
    {
        try {
            $roomId = $request->query('roomId');
            if (!$roomId) {
                return response()->json(['error' => 'Thiếu tham số roomId.'], 400);
            }

            $seats = $this->seatService->getSeatByRoomId($roomId);
            return response()->json($seats);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getSeatById(int $id)
    {
        try {
            $seat = $this->seatService->getSeatById($id);
            return response()->json($seat);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
