<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Services\Cast\ICastService;
use App\Services\Cinema\ICinemaService;
use App\Services\Movie\IMovieService;
use Exception;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieService;
    protected $cinemaService;
    protected $castService;

    public function __construct(
        IMovieService $movieService,
        ICinemaService $cinemaService,
        ICastService $castService
    ) {
        $this->movieService = $movieService;
        $this->cinemaService = $cinemaService;
        $this->castService = $castService;
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
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllMovie()
    {
        $movies = $this->movieService->getAllMovie();
        return response()->json($movies);
    }

    public function getTrailer($id)
    {
        try {
            $trailer = $this->movieService->getMovieTrailer($id);

            if (!$trailer) {
                return response()->json(['message' => 'Không tìm thấy trailer cho phim này'], 404);
            }

            return response()->json($trailer);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function moviedetail($id)
    {
        try {
            $movie = $this->movieService->getMovieById($id);
            $cinemas = $this->cinemaService->getAllCinema();
            $casts = $this->castService->getCastByMovieId($id);
            $trailer = $this->movieService->getMovieTrailer($id);

            return view('movies.moviedetail', compact('movie', 'cinemas', 'casts', 'trailer'));
        } catch (Exception $e) {
            abort(404, 'Phim không tồn tại');
        }
    }
}
