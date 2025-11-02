<?php

namespace App\Services\Movie;

use App\Http\Resources\TMDBMovieResource;
use App\Models\Cast;
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
    }

    public function onInit()
    {
        $this->syncMoviesFromTMDB();
        $this->generateCastsForMovie();
    }

    private function getNowPlayingMovies()
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/now_playing?api_key={$this->apiKey}&language=vi-VN&page=1";
            $response = Http::get($url);

            if (!$response->successful()) {
                Log::warning("Không thể lấy danh sách phim đang chiếu từ TMDB. URL: {$url}");
                return null;
            }

            $data = $response->json();
            Log::info("Đã lấy danh sách phim đang chiếu từ TMDB (" . count($data['results'] ?? []) . " phim)");
            return $data;
        } catch (Exception $e) {
            Log::error("Lỗi khi gọi TMDB API (NowPlaying): " . $e->getMessage());
            return null;
        }
    }

    private function getUpComingMovies()
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/upcoming?api_key={$this->apiKey}&language=vi-VN&page=1";
            $response = Http::get($url);

            if (!$response->successful()) {
                Log::warning("Không thể lấy danh sách phim sap chiếu từ TMDB. URL: {$url}");
                return null;
            }

            $data = $response->json();
            Log::info("Đã lấy danh sách phim sap chiếu từ TMDB (" . count($data['results'] ?? []) . " phim)");
            return $data;
        } catch (Exception $e) {
            Log::error("Lỗi khi gọi TMDB API (UpComing): " . $e->getMessage());
            return null;
        }
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

        $existingMovie = Movie::find($movieId);
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

            $casts = collect($castList)
                ->take(5) // chỉ lấy tối đa 5 diễn viên
                ->filter(fn($c) => !empty($c['name']))
                ->map(fn($c) => [
                    'movieid' => $movieId,
                    'actorname' => $c['name']
                ])
                ->values()
                ->toArray();

            return $casts;
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy cast cho movieId {$movieId}: " . $e->getMessage());
            return [];
        }
    }

    private function generateCastsForMovie()
    {
        $movieList = Movie::all();
        Log::info("Tìm thấy " . count($movieList) . " phim để xử lý cast.");

        foreach ($movieList as $movie) {
            Log::debug("Đang xử lý phim: {$movie->name} (ID: {$movie->id})");

            $existingCasts = $this->castRepository->findByMovieId($movie->id);

            if ($existingCasts->isNotEmpty()) {
                Log::debug("Phim {$movie->name} (ID: {$movie->id}) đã có cast, bỏ qua.");
                continue;
            }

            $castList = $this->getCasts($movie->id);
            foreach ($castList as $castData) {
                Cast::create([
                    'movieid' => $movie->id,
                    'actorname' => $castData['actorname']
                ]);

                Log::info("Đã lưu cast cho phim {$movie->name} (Diễn viên: {$castData['actorname']})");
            }
        }
    }


    public function getNowPlaying()
    {
        $response = $this->getNowPlayingMovies();

        if (empty($response) || empty($response['results'])) {
            Log::warning("Không có dữ liệu phim đang chiếu từ TMDB.");
            return [];
        }

        $movies = [];

        foreach ($response['results'] as $tmdbMovie) {
            $details = $this->getMovieDetails($tmdbMovie['id']);
            $movies[] = [
                'id' => $tmdbMovie['id'],
                'name' => $tmdbMovie['title'],
                'description' => !empty($tmdbMovie['overview']) ? $tmdbMovie['overview'] : 'Chưa có thông tin',
                'duration' => $details['runtime'] ?? 0,
                'releasedate' => !empty($tmdbMovie['release_date'])
                    ? Carbon::parse($tmdbMovie['release_date'])
                    : null,
                'posterurl' => !empty($tmdbMovie['poster_path'])
                    ? "https://image.tmdb.org/t/p/w500" . $tmdbMovie['poster_path']
                    : null,
                'bannerurl' => !empty($tmdbMovie['backdrop_path'])
                    ? "https://image.tmdb.org/t/p/w1280" . $tmdbMovie['backdrop_path']
                    : null,
                'agerating' => $details['agerating'] ?? 'ALL',
                'voteaverage' => $tmdbMovie['vote_average'] ?? 0,
                'director' => $details['director'] ?? null,
            ];
        }

        return $movies;
    }

    public function getUpComing()
    {
        $response = $this->getUpComingMovies();

        if (empty($response) || empty($response['results'])) {
            Log::warning("Không có dữ liệu phim sap chiếu từ TMDB.");
            return [];
        }

        $movies = [];

        foreach ($response['results'] as $tmdbMovie) {
            $details = $this->getMovieDetails($tmdbMovie['id']);
            $movies[] = [
                'id' => $tmdbMovie['id'],
                'name' => $tmdbMovie['title'],
                'description' => !empty($tmdbMovie['overview']) ? $tmdbMovie['overview'] : 'Chưa có thông tin',
                'duration' => $details['runtime'] ?? 0,
                'releasedate' => !empty($tmdbMovie['release_date'])
                    ? Carbon::parse($tmdbMovie['release_date'])
                    : null,
                'posterurl' => !empty($tmdbMovie['poster_path'])
                    ? "https://image.tmdb.org/t/p/w500" . $tmdbMovie['poster_path']
                    : null,
                'bannerurl' => !empty($tmdbMovie['backdrop_path'])
                    ? "https://image.tmdb.org/t/p/w1280" . $tmdbMovie['backdrop_path']
                    : null,
                'agerating' =>  $details['agerating'] ?? 'ALL',
                'voteaverage' => $tmdbMovie['vote_average'] ?? 0,
                'director' => $details['director'] ?? null,
            ];
        }

        return $movies;
    }

    // Old method
    // public function getNowPlaying()
    // {
    //     $response = $this->getNowPlayingMovies();

    //     if (empty($response) || empty($response['results'])) {
    //         Log::warning("Không có dữ liệu phim đang chiếu từ TMDB.");
    //         return [];
    //     }

    //     $movies = [];

    //     foreach ($response['results'] as $tmdbMovie) {
    //         $movies[] = [
    //             'id' => $tmdbMovie['id'],
    //             'name' => $tmdbMovie['title'],
    //             'description' => !empty($tmdbMovie['overview']) ? $tmdbMovie['overview'] : 'Chưa có thông tin',
    //             'duration' => $this->getMovieRunTime($tmdbMovie['id']),
    //             'releasedate' => !empty($tmdbMovie['release_date'])
    //                 ? Carbon::parse($tmdbMovie['release_date'])
    //                 : null,
    //             'posterurl' => !empty($tmdbMovie['poster_path'])
    //                 ? "https://image.tmdb.org/t/p/w500" . $tmdbMovie['poster_path']
    //                 : null,
    //             'bannerurl' => !empty($tmdbMovie['backdrop_path'])
    //                 ? "https://image.tmdb.org/t/p/w1280" . $tmdbMovie['backdrop_path']
    //                 : null,
    //             'agerating' => $this->getAgeCertification($tmdbMovie['id']),
    //             'voteaverage' => $tmdbMovie['vote_average'] ?? 0,
    //             'director' => $this->getDirector($tmdbMovie['id']),
    //         ];
    //     }

    //     return $movies;
    // }

    // public function getUpComing()
    // {
    //     $response = $this->getUpComingMovies();

    //     if (empty($response) || empty($response['results'])) {
    //         Log::warning("Không có dữ liệu phim sap chiếu từ TMDB.");
    //         return [];
    //     }

    //     $movies = [];

    //     foreach ($response['results'] as $tmdbMovie) {
    //         $movies[] = [
    //             'id' => $tmdbMovie['id'],
    //             'name' => $tmdbMovie['title'],
    //             'description' => !empty($tmdbMovie['overview']) ? $tmdbMovie['overview'] : 'Chưa có thông tin',
    //             'duration' => $this->getMovieRunTime($tmdbMovie['id']),
    //             'releasedate' => !empty($tmdbMovie['release_date'])
    //                 ? Carbon::parse($tmdbMovie['release_date'])
    //                 : null,
    //             'posterurl' => !empty($tmdbMovie['poster_path'])
    //                 ? "https://image.tmdb.org/t/p/w500" . $tmdbMovie['poster_path']
    //                 : null,
    //             'bannerurl' => !empty($tmdbMovie['backdrop_path'])
    //                 ? "https://image.tmdb.org/t/p/w1280" . $tmdbMovie['backdrop_path']
    //                 : null,
    //             'agerating' => $this->getAgeCertification($tmdbMovie['id']),
    //             'voteaverage' => $tmdbMovie['vote_average'] ?? 0,
    //             'director' => $this->getDirector($tmdbMovie['id']),
    //         ];
    //     }

    //     return $movies;
    // }

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
        return Movie::find($id);
    }

    private function getMovieDetails(int $movieId)
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$this->apiKey}&append_to_response=release_dates,credits&language=en-US";
            $response = Http::get($url);

            if (!$response->successful())
                return null;
            $data = $response->json();

            // Lấy runtime
            $runtime = $data['runtime'] ?? 0;

            // Lấy độ tuổi
            $certification = 'ALL';
            foreach ($data['release_dates']['results'] ?? [] as $country) {
                if ($country['iso_3166_1'] === 'US') {
                    $releaseDates = $country['release_dates'] ?? [];
                    if (!empty($releaseDates)) {
                        $certification = $this->mapAgeRating($releaseDates[0]['certification'] ?? '');
                        break;
                    }
                }
            }

            // Lấy đạo diễn
            $director = null;
            foreach ($data['credits']['crew'] ?? [] as $crew) {
                if (($crew['job'] ?? '') === 'Director') {
                    $director = $crew['name'];
                    break;
                }
            }

            return [
                'runtime' => $runtime,
                'agerating' => $certification,
                'director' => $director,
            ];
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy chi tiết phim {$movieId}: " . $e->getMessage());
            return null;
        }
    }

    public function getMovieTrailer(int $movieId)
    {
        try {
            $url = "https://api.themoviedb.org/3/movie/{$movieId}/videos?api_key={$this->apiKey}&language=en-US";
            $response = Http::get($url);

            if (!$response->successful()) {
                Log::warning("Không thể lấy trailer cho movieId {$movieId} từ TMDB.");
                return null;
            }

            $data = $response->json();
            $videos = $data['results'] ?? [];

            if (empty($videos)) {
                Log::info("Không có video nào cho movieId {$movieId}.");
                return null;
            }

            $trailer = collect($videos)->first(function ($video) {
                return strtolower($video['site'] ?? '') === 'youtube'
                    && strtolower($video['type'] ?? '') === 'trailer';
            });

            if ($trailer) {
                return [
                    'name' => $trailer['name'],
                    'key' => $trailer['key'],
                    'url' => "https://www.youtube.com/watch?v=" . $trailer['key'],
                    'embed_url' => "https://www.youtube.com/embed/" . $trailer['key'],
                ];
            }

            // fallback: nếu không có trailer, lấy video đầu tiên
            $firstVideo = $videos[0];
            return [
                'name' => $firstVideo['name'],
                'key' => $firstVideo['key'],
                'url' => "https://www.youtube.com/watch?v=" . $firstVideo['key'],
                'embed_url' => "https://www.youtube.com/embed/" . $firstVideo['key'],
            ];
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy trailer cho movieId {$movieId}: " . $e->getMessage());
            return null;
        }
    }
}
