<?php

namespace App\Repositories\Movie;

use App\Models\Movie;

class MovieRepository implements IMovieRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function existsByName(string $name): bool
    {
        return Movie::where('name', $name)->exists();
    }

    public function getAllPaginated(int $perPage)
    {
        return Movie::paginate($perPage);
    }

    public function findByIds(array $ids)
    {
        return Movie::whereIn('id', $ids)->get();
    }
}
