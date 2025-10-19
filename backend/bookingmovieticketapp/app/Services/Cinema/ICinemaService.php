<?php

namespace App\Services\Cinema;

use App\Http\Requests\CinemaRequest;
use App\Models\Cinema;
use Carbon\Carbon;

interface ICinemaService
{
    public function createCinema(CinemaRequest $cinemaRequest);
    public function updateCinema(int $id, CinemaRequest $cinemaRequest);
    public function getAllCinema();
    public function getCinemaByMovieIdAndCityAndDate(int $movieId, string $city, Carbon  $date);
    public function getCinemaById(int $id);
    public function saveCinema(Cinema $cinema);
    public function updateCinemaStatus(int $id, bool $isActive);
}
