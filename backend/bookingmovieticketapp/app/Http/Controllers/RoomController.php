<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Services\Room\IRoomService;
use App\Services\Seat\ISeatService;
use Exception;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected $roomService;
    protected $seatService;

    public function __construct(IRoomService $roomService, ISeatService $seatService)
    {
        $this->roomService = $roomService;
        $this->seatService = $seatService;
    }

    public function getRoomById(int $id)
    {
        try {
            $room = $this->roomService->getRoomById($id);
            return response()->json($room);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getSeatsByRoomId(int $roomId)
    {
        try {
            $seats = $this->seatService->getSeatByRoomId($roomId);
            return response()->json($seats);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllRooms()
    {
        try {
            $rooms = $this->roomService->getAllRooms();
            return response()->json($rooms);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createRoom(RoomRequest $request)
    {
        try {
            $room = $this->roomService->createRoom($request);
            return response()->json($room);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateRoom(int $id, RoomRequest $request)
    {
        try {
            $room = $this->roomService->updateRoom($id, $request);
            return response()->json($room);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteRoom(int $id)
    {
        try {
            $this->roomService->deleteRoom($id);
            return response()->json(['message' => 'Xóa phòng thành công.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getRoomsByCinemaId(int $cinemaId)
    {
        try {
            $rooms = $this->roomService->getRoomByCinemaId($cinemaId);
            return response()->json($rooms);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
