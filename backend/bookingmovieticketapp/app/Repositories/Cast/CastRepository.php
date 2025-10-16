<?php

namespace App\Repositories\Cast;

use App\Models\Cast;

class CastRepository implements ICastRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByMovieId(int $movieId)
    {
        return Cast::where('movieid', $movieId)->get();
    }
}
