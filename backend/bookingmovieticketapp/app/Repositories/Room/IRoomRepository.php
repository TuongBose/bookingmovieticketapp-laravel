<?php

namespace App\Repositories\Room;

use App\Models\Cinema;

interface IRoomRepository
{
    public function findByCinema(Cinema $cinema);
    public function findByCinemaIdIn(array $cinemaIds);
}
