<?php

namespace App\Http\Controllers;

use App\Services\Movie\IMovieService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $movieService;

    public function __construct(IMovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function index()
    {
        $nowPlaying = $this->movieService->getNowPlaying();
        $upComing = $this->movieService->getUpComing();

        return view('home', compact('nowPlaying', 'upComing'));
    }
}
