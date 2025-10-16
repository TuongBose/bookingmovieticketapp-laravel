<?php

namespace App\Repositories\Seat;

use App\Models\Room;

interface ISeatRepository
{
    public function findByRoom(Room $room);
    public function existsByRoomIdAndSeatnumber(int $roomId, string $seatNumber): bool;
    public function countByRoom(Room $room): int;
}
