<?php

namespace App\Services\Cast;

interface ICastService
{
    public function getCastByMovieId(int $movieId);
}
