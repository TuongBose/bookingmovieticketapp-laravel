<?php

namespace App\Services\Room;

use App\Http\Requests\RoomRequest;

interface IRoomService
{
    public function createRoom(RoomRequest $roomRequest);
    public function getRoomById(int $id);
    public function updateRoom(int $id, RoomRequest $roomRequest);
    public function getRoomByCinemaId(int $cinemaId);
    public function getAllRooms();
    public function deleteRoom(int $id);
}
