<?php

namespace App\Repositories\Cast;

interface ICastRepository
{
    public function findByMovieId(int $movieId);
}
