<?php

namespace App\Services\Movie;

use App\Http\Resources\TMDBMovieResource;
use App\Http\Resources\TMDBNowPlayingResource;
use App\Http\Resources\TMDBUpComingResource;
use App\Models\Movie;
use App\Repositories\Cast\ICastRepository;
use App\Repositories\Movie\IMovieRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MovieService implements IMovieService
{
    protected $movieRepository;
    protected $castRepository;
    protected $apiKey;
    /**
     * Create a new class instance.
     */
    public function __construct(
        IMovieRepository $movieRepository,
        ICastRepository $castRepository
    ) {
        $this->movieRepository = $movieRepository;
        $this->castRepository = $castRepository;
        $this->apiKey = env('TMDB_API_KEY');

        $this->syncMoviesFromTMDB();
        $this->generateCastsForMovie();
    }

    private function getNowPlayingMovies()
    {
        $url = "https://api.themoviedb.org/3/movie/now_playing?api_key={$this->apiKey}&language=vi-VN&page=1";
    }

    private function getUpComingMovies()
    {
    }

    private function getAgeCertification(int $movieId)
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/{$movieId}/release_dates?api_key={$this->apiKey}";
            $response = Http::get($url);

            if (!$response->successful())
                return 'ALL';
            $data = $response->json();

            foreach ($data['results'] ?? [] as $country) {
                if ($country['iso_3166_1'] === 'US') {
                    $releaseDates = $country['release_dates'] ?? [];
                    if (!empty($releaseDates)) {
                        $cert = $releaseDates[0]['certification'] ?? 'ALL';
                        return $this->mapAgeRating($cert);
                    }
                }
            }
            return 'ALL';
        } catch (Exception $e) {
            return 'ALL';
        }
    }

    private function mapAgeRating(?string $cert)
    {
        return match ($cert) {
            'PG' => 'K',
            'PG-13' => 'T13',
            'R' => 'T16',
            'NC-17' => 'C18',
            default => 'ALL',
        };
    }

    private function getDirector(int $movieId)
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$this->apiKey}";
            $response = Http::get($url);

            if ($response->successful()) {
                foreach ($response->json()['crew'] ?? [] as $crew) {
                    if (($crew['job'] ?? '') === 'Director') {
                        return $crew['name'] ?? null;
                    }
                }
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    private function getMovieRunTime(int $movieId)
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$this->apiKey}&language=en-US";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $data['runtime'] ?? 0;
            }

            return 0;
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy runtime phim {$movieId}: " . $e->getMessage());
            return 0;
        }
    }

    private function saveTMDBMovie(TMDBMovieResource $tmdbMovie, string $type = '')
    {
        $movieId = $tmdbMovie->id ?? null;
        if (!$movieId)
            return;

        $existingMovie = Movie::findOrFail($movieId);
        if ($existingMovie) {
            Log::debug("Phim {$tmdbMovie->title} đã tồn tại, bỏ qua.");
            return;
        }

        $newMovieData = Movie::create([
            'id' => $tmdbMovie->id,
            'name' => $tmdbMovie->title,
            'description' => empty($tmdbMovie->overview) ? 'Chưa có thông tin' : $tmdbMovie->overview,
            'duration' => $this->getMovieRunTime($tmdbMovie->id),
            'releasedate' => !empty($tmdbMovie->release_date) ? Carbon::parse($tmdbMovie->release_date) : null,
            'posterurl' => $tmdbMovie->poster_path ? "https://image.tmdb.org/t/p/w500" . $tmdbMovie->poster_path : null,
            'bannerurl' => $tmdbMovie->backdrop_path ? "https://image.tmdb.org/t/p/w1280" . $tmdbMovie->backdrop_path : null,
            'agerating' => $this->getAgeCertification($tmdbMovie->id),
            'voteaverage' => $tmdbMovie->vote_average ?? 0,
            'director' => $this->getDirector($tmdbMovie->id),
        ]);

        Log::info("Đã thêm phim {$type}: {$tmdbMovie->title}");
    }


    private function syncMoviesFromTMDB()
    {
        // Sync NowPlaying
        $responseNowPlaying = $this->getNowPlayingMovies();
        $nowPlayingMovies = $responseNowPlaying['results'] ?? [];

        foreach ($nowPlayingMovies as $tmdbMovieData) {
            $tmdbMovie = new TMDBMovieResource((object) $tmdbMovieData);
            $this->saveTMDBMovie($tmdbMovie, 'Đang chiếu');
        }

        // Sync UpPlaying
        $responseUpComing = $this->getUpComingMovies();
        $upComingMovies = $responseUpComing['results'] ?? [];

        foreach ($upComingMovies as $tmdbMovieData) {
            $tmdbMovie = new TMDBMovieResource((object) $tmdbMovieData);
            $this->saveTMDBMovie($tmdbMovie, 'Sắp chiếu');
        }
    }

    private function getCasts(int $movieId)
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$this->apiKey}";

            $response = Http::get($url);

            if (!$response->successful()) {
                Log::warning("Không thể lấy cast từ TMDB cho movieId {$movieId}");
                return [];
            }

            $data = $response->json();
            $castList = $data['cast'] ?? [];

            if (empty($castList)) {
                Log::warning("Không có cast nào cho movieId {$movieId}");
                return [];
            }

            $casts = [];
            foreach ($castList as $index => $cast) {
                if ($index >= 5)
                    break; // chỉ lấy tối đa 5 diễn viên
                $actorName = $cast['name'] ?? null;
                if (empty($actorName))
                    continue;

                $casts[] = [
                    'actorname' => $actorName
                ];
            }

            return $casts;
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy cast cho movieId {$movieId}: " . $e->getMessage());
            return [];
        }
    }

    public function getNowPlaying()
    {
        $url = "https://api.themoviedb.org/3/movie/now_playing?api_key={$this->apiKey}&language=vi-VN&page=1";
        $response = Http::get($url);

        if (!$response->successful()) {
            Log::warning("Không thể lấy dữ liệu phim đang chiếu từ TMDB.");
            return [];
        }

        $data = $response->json();
        $movies = [];

        foreach ($data['results'] ?? [] as $tmdbMovie) {
            $movies[] = [
                'id' => $tmdbMovie['id'],
                'name' => $tmdbMovie['title'],
                'description' => $tmdbMovie['overview'] ?: 'Chưa có thông tin',
                'duration' => $this->getMovieRunTime($tmdbMovie['id']),
                'releasedate' => Carbon::parse($tmdbMovie['release_date']),
                'posterurl' => "https://image.tmdb.org/t/p/w500" . $tmdbMovie['poster_path'],
                'bannerurl' => "https://image.tmdb.org/t/p/w1280" . $tmdbMovie['backdrop_path'],
                'agerating' => $this->getAgeCertification($tmdbMovie['id']),
                'voteaverage' => $tmdbMovie['vote_average'],
                'director' => $this->getDirector($tmdbMovie['id']),
            ];
        }

        return $movies;
    }

    public function getUpComing()
    {
        $url = "https://api.themoviedb.org/3/movie/upcoming?api_key={$this->apiKey}&language=vi-VN&page=1";
        $response = Http::get($url);

        if (!$response->successful()) {
            Log::warning("Không thể lấy dữ liệu phim sắp chiếu từ TMDB.");
            return [];
        }

        $data = $response->json();
        $movies = [];

        foreach ($data['results'] ?? [] as $tmdbMovie) {
            $movies[] = [
                'id' => $tmdbMovie['id'],
                'name' => $tmdbMovie['title'],
                'description' => $tmdbMovie['overview'] ?: 'Chưa có thông tin',
                'duration' => $this->getMovieRunTime($tmdbMovie['id']),
                'releasedate' => Carbon::parse($tmdbMovie['release_date']),
                'posterurl' => "https://image.tmdb.org/t/p/w500" . $tmdbMovie['poster_path'],
                'bannerurl' => "https://image.tmdb.org/t/p/w1280" . $tmdbMovie['backdrop_path'],
                'agerating' => $this->getAgeCertification($tmdbMovie['id']),
                'voteaverage' => $tmdbMovie['vote_average'],
                'director' => $this->getDirector($tmdbMovie['id']),
            ];
        }

        return $movies;
    }

    public function getAllMovie()
    {
        return Movie::all();
    }

    public function existsByName(string $name)
    {
        return $this->movieRepository->existsByName($name);
    }

    public function getMovieById(int $id)
    {
        return Movie::findOrFail($id);
    }

}
