<?php

namespace App\Services\Cast;

use App\Http\Resources\CastResource;
use App\Models\Cast;
use App\Models\Movie;
use App\Repositories\Cast\ICastRepository;
use App\Repositories\Movie\IMovieRepository;

class CastService implements ICastService
{
    protected $castRepository;
    protected $movieRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        ICastRepository $castRepository,
        IMovieRepository $movieRepository
    ) {
        $this->castRepository = $castRepository;
        $this->movieRepository = $movieRepository;
    }

    public function getCastByMovieId(int $movieId)
    {
        $movie = Movie::findOrFail($movieId);
        $casts = Cast::where('movieid', $movieId)->get();
        return CastResource::collection($casts);
    }
}
