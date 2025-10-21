<?php

namespace App\Services\ShowTime;

use App\Http\Requests\ShowtimeRequest;
use Carbon\Carbon;

interface IShowTimeService
{
    public function getShowTimeByMovieIdAndCinemaIdAndDate(int $movieId, int $cinemaId, Carbon $date);
    public function getShowTimeById(int $id);
    public function updateShowTimeStatus(int $id, bool $isActive);
    public function getBookingsCountForShowTime(int $showTimeId);
    public function createShowTime(ShowtimeRequest $showtimeRequest);
}
