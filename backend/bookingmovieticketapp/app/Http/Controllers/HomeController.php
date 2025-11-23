<?php

namespace App\Http\Controllers;

use App\Services\Movie\IMovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    protected $movieService;

    public function __construct(IMovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function index(Request $request)
    {
        // Lấy dữ liệu từ cache hoặc API
        $nowPlayingArray = Cache::remember('movies_now_playing', 60 * 10, function () {
            return $this->movieService->getNowPlaying();
        });
        $upComingArray = Cache::remember('movies_upcoming', 60 * 10, function () {
            return $this->movieService->getUpComing();
        });

        // Ép thành Collection nếu chưa phải
        $nowPlayingCollection = $nowPlayingArray instanceof Collection ? $nowPlayingArray : collect($nowPlayingArray);
        $upComingCollection = $upComingArray instanceof Collection ? $upComingArray : collect($upComingArray);

        // Lấy page hiện tại, default = 1
        $nowPage = (int) $request->query('now_page', 1);
        $upPage  = (int) $request->query('up_page', 1);

        // Phân trang collection
        $nowPlaying = $this->paginateCollection($nowPlayingCollection, 4, $nowPage, 'now_page');
        $upComing   = $this->paginateCollection($upComingCollection, 4, $upPage, 'up_page');

        return view('home', compact('nowPlaying', 'upComing'));
    }

    /**
     * Hàm helper phân trang Collection
     * @param Collection $collection
     * @param int $perPage
     * @param int $currentPage
     * @param string $pageName
     * @return LengthAwarePaginator
     */
    private function paginateCollection(Collection $collection, int $perPage, int $currentPage, string $pageName): LengthAwarePaginator
    {
        $currentPage = max($currentPage, 1); // đảm bảo >= 1
        $items = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => url()->current(),
                'pageName' => $pageName,
            ]
        );
    }
}
