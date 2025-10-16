<?php

namespace App\Repositories\Cinema;

interface ICinemaRepository
{
    public function findByCity(string $city);
}
