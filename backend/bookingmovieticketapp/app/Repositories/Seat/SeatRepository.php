<?php

namespace App\Repositories\Seat;

use App\Models\Room;
use App\Models\Seat;

class SeatRepository implements ISeatRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByRoom(Room $room)
    {
        return Seat::where('roomid', $room->id)->get();
    }

    public function existsByRoomIdAndSeatnumber(int $roomId, string $seatNumber): bool
    {
        return Seat::where('roomid', $roomId)
                    ->where('seatnumber', $seatNumber)
                    ->exists();
    }

    public function countByRoom(Room $room): int
    {
        return Seat::where('roomid', $room->id)->count();
    }
}
