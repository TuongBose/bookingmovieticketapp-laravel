<?php

namespace App\Services\Cinema;

use App\Http\Requests\CinemaRequest;
use App\Models\Cinema;
use App\Models\Movie;
use App\Repositories\Cinema\ICinemaRepository;
use App\Repositories\Movie\IMovieRepository;
use App\Repositories\Room\IRoomRepository;
use App\Repositories\ShowTime\IShowTimeRepository;
use Carbon\Carbon;

class CinemaService implements ICinemaService
{
    protected $cinemaRepository;
    protected $showTimeRepository;
    protected $roomRepository;
    protected $movieRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        ICinemaRepository $cinemaRepository,
        IShowTimeRepository $showTimeRepository,
        IRoomRepository $roomRepository,
        IMovieRepository $movieRepository
    ) {
        $this->cinemaRepository = $cinemaRepository;
        $this->showTimeRepository = $showTimeRepository;
        $this->roomRepository = $roomRepository;
        $this->movieRepository = $movieRepository;
    }

    public function createCinema(CinemaRequest $cinemaRequest)
    {
        return Cinema::create([
            'name' => $cinemaRequest->name,
            'city' => $cinemaRequest->city,
            'coordinates' => $cinemaRequest->coordinates,
            'address' => $cinemaRequest->address,
            'phonenumber' => $cinemaRequest->phonenumber,
            'maxroom' => $cinemaRequest->maxroom,
            'imagename' => $cinemaRequest->imagename,
            'isactive' => $cinemaRequest->isactive,
        ]);
    }

    public function updateCinema(int $id, CinemaRequest $cinemaRequest)
    {
        $cinema = Cinema::findOrFail($id);
        $cinema->update([
            'name' => $cinemaRequest->name,
            'city' => $cinemaRequest->city,
            'coordinates' => $cinemaRequest->coordinates,
            'address' => $cinemaRequest->address,
            'phonenumber' => $cinemaRequest->phonenumber,
            'maxroom' => $cinemaRequest->maxroom,
            'isactive' => $cinemaRequest->isactive,
        ]);
        return $cinema;
    }

    public function getAllCinema()
    {
        return Cinema::all();
    }

    public function getCinemaByMovieIdAndCityAndDate(int $movieId, string $city, Carbon $date)
    {
        $movie = Movie::findOrFail($movieId);
        $cinemas = ($city === 'all') ? Cinema::all() : $this->cinemaRepository->findByCity($city);

        $filteredCinemas = [];

        foreach ($cinemas as $cinema) {
            if (!$cinema->isactive) {
                continue;
            }

            $rooms = $this->roomRepository->findByCinema($cinema);
            $roomIds = $rooms->pluck('id')->toArray();

            if (empty($roomIds)) {
                continue;
            }

            // Lấy các suất chiếu phù hợp (phim + phòng + ngày)
            $showtimes = $this->showTimeRepository->findByMovieAndRoomIdInAndShowdate($movie, $roomIds, $date->toDateString());

            if ($showtimes->isNotEmpty()) {
                $filteredCinemas[] = $cinema;
            }
        }

        return $filteredCinemas;
    }

    public function getCinemaById(int $id)
    {
        return Cinema::findOrFail($id);
    }

    public function saveCinema(Cinema $cinema)
    {
        $cinema->save();
    }

    public function updateCinemaStatus(int $id, bool $isActive)
    {
        $cinema = Cinema::findOrFail($id);
        $cinema->isactive = $isActive;
        $cinema->save();
    }
}
