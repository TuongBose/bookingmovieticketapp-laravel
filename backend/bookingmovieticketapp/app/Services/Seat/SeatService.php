<?php

namespace App\Services\Seat;

use App\Http\Requests\SeatRequest;
use App\Http\Resources\SeatResource;
use App\Models\Room;
use App\Models\Seat;
use App\Repositories\Room\IRoomRepository;
use App\Repositories\Seat\ISeatRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class SeatService implements ISeatService
{
    protected $roomRepository;
    protected $seatRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        IRoomRepository $roomRepository,
        ISeatRepository $seatRepository
    ) {
        $this->roomRepository = $roomRepository;
        $this->seatRepository = $seatRepository;
    }

    public function generateSeatsForAllRooms()
    {
        $rooms = Room::all();
        foreach ($rooms as $room) {
            $this->generateSeatsForRoom($room);
        }
    }

    private function generateSeatsForRoom(Room $room)
    {
        $roomId = $room->id;
        $seatColumnMax = $room->seatcolumnmax;
        $seatRowMax = $room->seatrowmax;

        $existingSeats = $this->seatRepository->findByRoom($room);
        if (count($existingSeats) > 0) {
            Log::info("Phòng {$room->name} (ID: {$room->id}) đã có danh sách ghế.");
            return;
        }

        for ($row = 0; $row < $seatRowMax; $row++) {
            $rowChar = chr(ord('A') + $row); // chuyển 0 -> A, 1 -> B,...

            for ($col = 1; $col <= $seatColumnMax; $col++) {
                $seatNumber = "{$rowChar}{$col}";

                // Kiểm tra nếu ghế đã tồn tại (phòng + số ghế)
                if ($this->seatRepository->existsByRoomIdAndSeatnumber($roomId, $seatNumber)) {
                    continue;
                }

                // Tạo ghế mới
                Seat::create([
                    'roomid' => $roomId,
                    'seatnumber' => $seatNumber,
                ]);
                Log::info("Đã tạo ghế {$seatNumber} cho phòng {$room->name} (ID: {$room->id})");
            }
        }
    }

    public function createSeat(SeatRequest $seatRequest)
    {
        $room = Room::findOrFail($seatRequest->roomid);

        $newSeat = Seat::create([
            'roomid' => $room->roomid,
            'seatnumber' => $seatRequest->seatnumber,
        ]);
        return $newSeat;
    }

    public function getSeatById(int $id)
    {
        $existingSeat = Seat::findOrFail($id);
        return new SeatResource($existingSeat);
    }

    public function updateSeat(int $id, SeatRequest $seatRequest)
    {
        $existingSeat = Seat::findOrFail($id);
        $existingRoom = Room::findOrFail($seatRequest->roomid);

        $existingSeat->update([
            'roomid' => $existingRoom->id,
            'seatnumber' => $seatRequest->seatnumber
        ]);

        return $existingSeat;
    }

    public function getSeatByRoomId(int $roomId)
    {
        $existingRoom = Room::findOrFail($roomId);
        $seats = $this->seatRepository->findByRoom($existingRoom);

        if (count($seats) === 0) {
            throw new Exception("Không có danh sách ghế trong phòng này");
        } else {
            return SeatResource::collection($seats);
        }
    }
}
