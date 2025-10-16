<?php

namespace App\Repositories\Room;

use App\Models\Cinema;
use App\Models\Room;

class RoomRepository implements IRoomRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByCinema(Cinema $cinema)
    {
        return Room::where('cinemaid', $cinema->id)->get();
    }

    public function findByCinemaIdIn(array $cinemaIds)
    {
        return Room::whereIn('cinemaid', $cinemaIds)->get();
    }
}
