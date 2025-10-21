<?php

namespace App\Http\Controllers;

use App\Services\Movie\IMovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieService;

    public function __construct(IMovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function getNowPlaying()
    {
        $movies = $this->movieService->getNowPlaying();
        return response()->json($movies);
    }

    public function getUpComing()
    {
        $movies = $this->movieService->getUpComing();
        return response()->json($movies);
    }

    // public function getSimilarMovies(int $movieId)
    // {
    //     $movies = $this->movieService->getSimilarMovies($movieId);
    //     return response()->json($movies);
    // }

    public function getMovieById(int $id)
    {
        try {
            $movie = $this->movieService->getMovieById($id);
            return response()->json($movie);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllMovie()
    {
        $movies = $this->movieService->getAllMovie();
        return response()->json($movies);
    }
}
