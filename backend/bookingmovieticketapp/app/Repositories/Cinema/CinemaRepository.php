<?php

namespace App\Repositories\Cinema;

use App\Models\Cinema;

class CinemaRepository implements ICinemaRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByCity(string $city)
    {
        return Cinema::where('city', $city)->get();
    }
}
