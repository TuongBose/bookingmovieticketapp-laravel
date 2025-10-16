<?php

namespace App\Repositories\ShowTime;

use App\Models\Movie;
use App\Models\ShowTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShowTimeRepository implements IShowTimeRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByMovieAndRoomIdInAndShowdateBetween(Movie $movie, array $roomIds, Carbon $start, Carbon $end): Collection
    {
        return ShowTime::where('movieid', $movie->id)
            ->whereIn('roomid', $roomIds)
            ->whereBetween('showdate', [$start, $end])
            ->get();
    }

    public function findByMovieAndRoomIdInAndShowdate(Movie $movie, array $roomIds, string $showdate): Collection
    {
        return ShowTime::where('movieid', $movie->id)
            ->whereIn('roomid', $roomIds)
            ->whereDate('showdate', $showdate)
            ->get();
    }

    public function findByMovieIdAndRoomIdIn(int $movieId, array $roomIds): Collection
    {
        return ShowTime::where('movieid', $movieId)
            ->whereIn('roomid', $roomIds)
            ->get();
    }

    public function findByRoomIdAndShowdate(int $roomId, string $showdate): Collection
    {
        return ShowTime::where('roomid', $roomId)
            ->whereDate('showdate', $showdate)
            ->get();
    }

    public function findByCinemaIdAndShowdate(int $cinemaId, string $showdate): Collection
    {
        return ShowTime::whereIn('roomid', function ($query) use ($cinemaId) {
                $query->select('id')
                      ->from('rooms')
                      ->where('cinemaid', $cinemaId);
            })
            ->whereDate('showdate', $showdate)
            ->get();
    }
}
