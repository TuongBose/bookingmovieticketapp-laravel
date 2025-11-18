<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Services\Cast\ICastService;
use App\Services\Cinema\ICinemaService;
use App\Services\Movie\IMovieService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Movie;
 use Illuminate\Support\Facades\Log;

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
        $movies = Cache::remember('movies_now_playing', 600, function () {
            return $this->movieService->getNowPlaying();
        });

        return response()->json($movies);
    }

    public function getUpComing()
    {
        $movies = Cache::remember('movies_upcoming', 600, function () {
            return $this->movieService->getUpComing();
        });

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
        $movies = Cache::remember('movies_all', 600, function () {
            return $this->movieService->getAllMovie();
        });
        
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
            $movie = $this->movieService->getMovieById((int)$id);
            $cinemas = $this->cinemaService->getAllCinema();
            $casts = $this->castService->getCastByMovieId((int)$id);
            $trailer = $this->movieService->getMovieTrailer((int)$id);

            return view('movies.moviedetail', compact('movie', 'cinemas', 'casts', 'trailer'));
        } catch (Exception $e) {
            abort(404, 'Phim không tồn tại');
        }
    }
    //movie search
    //     public function searchPage(Request $request)
    // {
    //     $keyword = trim($request->get('q', ''));

    //     $movies = [];
    //     if ($keyword) {
    //         $movies = Movie::where('name', 'LIKE', "%{$keyword}%")
    //             ->select('id', 'name', 'posterurl', 'releasedate','agerating')
    //             ->limit(20)
    //             ->get();
    //     }

    //     return view('search.results', compact('movies', 'keyword'));
    // }
   
    public function searchPage(Request $request)
    {
        $keyword = trim($request->get('q', ''));

        $movies = collect();

        // Chỉ tìm khi có từ khóa
        if ($keyword !== '') {
            $movies = Movie::where('name', 'LIKE', "%{$keyword}%")
                ->select('id', 'name', 'posterurl', 'releasedate', 'duration')
                ->limit(20)
                ->get();
        }

        return view('search.results', compact('movies', 'keyword'));
    }


    

}
