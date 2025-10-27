<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Services\Cinema\ICinemaService;
use App\Services\Movie\IMovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieService;
    protected $cinemaService;

    public function __construct(IMovieService $movieService, ICinemaService $cinemaService)
    {
        $this->movieService = $movieService;
        $this->cinemaService = $cinemaService;
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

    public function show($id)
    {
        try {
            $movie = $this->movieService->getMovieById($id);
            $cinemas = $this->cinemaService->getAllCinema(); 

            return view('movies.show', compact('movie', 'cinemas'));
        } catch (\Exception $e) {
            abort(404, 'Phim không tồn tại');
        }
    }
}
