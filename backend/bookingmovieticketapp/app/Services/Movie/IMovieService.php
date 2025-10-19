<?php

namespace App\Services\Movie;

interface IMovieService
{
    public function getNowPlaying();
    public function getUpComing();
    public function getAllMovie();
    public function existsByName(string $name);
    public function getMovieById(int $id);
}
