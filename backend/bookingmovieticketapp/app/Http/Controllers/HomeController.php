<?php

namespace App\Http\Controllers;

use App\Services\Movie\IMovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    protected $movieService;

    public function __construct(IMovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function index()
    {
        $nowPlaying = Cache::remember('movies_now_playing', 60 * 10, function () {
            return $this->movieService->getNowPlaying();
        });

        $upComing = Cache::remember('movies_upcoming', 60 * 10, function () {
            return $this->movieService->getUpComing();
        });

        return view('home', compact('nowPlaying', 'upComing'));
    }
}
