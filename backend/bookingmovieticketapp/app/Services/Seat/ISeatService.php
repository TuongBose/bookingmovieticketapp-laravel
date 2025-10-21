<?php

namespace App\Services\Seat;

use App\Http\Requests\SeatRequest;

interface ISeatService
{
    public function createSeat(SeatRequest $seatRequest) ;
    public function getSeatById(int $id) ;
    public function updateSeat(int $id, SeatRequest $seatRequest) ;
    public function getSeatByRoomId(int $roomId) ;
}
