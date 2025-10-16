<?php

namespace App\Repositories\Movie;

interface IMovieRepository
{
    public function existsByName(string $name): bool;
    public function getAllPaginated(int $perPage);
    public function findByIds(array $ids);
}
