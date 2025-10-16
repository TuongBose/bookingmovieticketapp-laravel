<?php

namespace App\Repositories\ShowTime;

use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface IShowTimeRepository
{
    public function findByMovieAndRoomIdInAndShowdateBetween(Movie $movie, array $roomIds, Carbon $start, Carbon $end): Collection;

    public function findByMovieAndRoomIdInAndShowdate(Movie $movie, array $roomIds, string $showdate): Collection;

    public function findByMovieIdAndRoomIdIn(int $movieId, array $roomIds): Collection;

    public function findByRoomIdAndShowdate(int $roomId, string $showdate): Collection;

    public function findByCinemaIdAndShowdate(int $cinemaId, string $showdate): Collection;
}
