<?php

namespace App\Services\ShowTime;

use App\Http\Requests\ShowtimeRequest;
use App\Http\Resources\ShowTimeResource;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Room;
use App\Models\ShowTime;
use App\Repositories\Booking\IBookingRepository;
use App\Repositories\BookingDetail\IBookingDetailRepository;
use App\Repositories\Cinema\ICinemaRepository;
use App\Repositories\Movie\IMovieRepository;
use App\Repositories\Room\IRoomRepository;
use App\Repositories\ShowTime\IShowTimeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ShowTimeService implements IShowTimeService
{
    protected $showTimeRepository;
    protected $roomRepository;
    protected $cinemaRepository;
    protected $movieRepository;
    protected $bookingRepository;
    protected $bookingDetailRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        IShowTimeRepository $showTimeRepository,
        IRoomRepository $roomRepository,
        ICinemaRepository $cinemaRepository,
        IMovieRepository $movieRepository,
        IBookingRepository $bookingRepository,
        IBookingDetailRepository $bookingDetailRepository
    ) {
        $this->showTimeRepository = $showTimeRepository;
        $this->roomRepository = $roomRepository;
        $this->cinemaRepository = $cinemaRepository;
        $this->movieRepository = $movieRepository;
        $this->bookingRepository = $bookingRepository;
        $this->bookingDetailRepository = $bookingDetailRepository;

        $this->generateShowtimesForAllRooms();
    }

    private function generateShowtimesForAllRooms()
    {
        $rooms = Room::all();
        $movies = Movie::all();
        $random = random_int(1, 10000);

        if ($rooms->isEmpty() || $movies->isEmpty()) {
            Log::info("Không có phim hoặc phòng nào để tạo lịch chiếu.");
            return;
        }

        $startDate = Carbon::tomorrow();
        $endDate = (clone $startDate)->addDays(7);

        $timeSlots = [
            Carbon::createFromTime(7, 0),
            Carbon::createFromTime(10, 0),
            Carbon::createFromTime(13, 0),
            Carbon::createFromTime(16, 0),
            Carbon::createFromTime(19, 0),
        ];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            foreach ($rooms as $room) {
                foreach ($timeSlots as $slot) {
                    $startTime = Carbon::parse($date->toDateString() . ' ' . $slot->toTimeString());

                    // Kiểm tra xem slot này có trùng không
                    $existingShowtimes = $this->showTimeRepository->findByRoomIdAndShowdate($room->id, $date->toDateString());
                    $isAvailable = true;

                    foreach ($existingShowtimes as $existing) {
                        $existingMovie = Movie::findOrFail($existing->movieid);
                        $duration = $existingMovie?->duration ?? 120;
                        $existingStart = Carbon::parse($existing->starttime);
                        $existingEnd = $existingStart->copy()->addMinutes($duration);

                        if ($startTime->between($existingStart, $existingEnd)) {
                            $isAvailable = false;
                            break;
                        }
                    }

                    if (!$isAvailable) {
                        continue;
                    }

                    // Chọn phim ngẫu nhiên
                    $movie = $movies[($room->id + $date->day) % count($movies)];
                    $price = 80000 + random_int(0, 70000);

                    $showtime = ShowTime::create([
                        'movieid' => $movie->id,
                        'roomid' => $room->id,
                        'showdate' => $date->toDateString(),
                        'starttime' => $startTime,
                        'price' => $price,
                        'isactive' => true,
                    ]);

                    Log::info("🎬 Đã tạo lịch chiếu phim {$movie->name} tại phòng {$room->name} ({$startTime}).");
                }
            }
        }
    }

    public function getShowTimeByMovieIdAndCinemaIdAndDate(int $movieId, int $cinemaId, Carbon $date)
    {
        $cinema = Cinema::findOrFail($cinemaId);
        $movie = Movie::findOrFail($movieId);
        $rooms = $this->roomRepository->findByCinema($cinema);
        $roomIds = collect($rooms)->pluck('id')->toArray();

        $showtimes = $this->showTimeRepository->findByMovieAndRoomIdInAndShowdate($movie, $roomIds, $date);

        return ShowTimeResource::collection($showtimes);
    }

    public function getShowTimeById(int $id)
    {
        $showtime = ShowTime::findOrFail($id);
        return new ShowTimeResource($showtime);
    }

    public function updateShowTimeStatus(int $id, bool $isActive)
    {
        $showtime = ShowTime::findOrFail($id);
        $showtime->update(['isactive' => $isActive]);
    }

    public function getBookingsCountForShowTime(int $showTimeId)
    {
        $bookings = $this->bookingRepository->findByShowTimeId($showTimeId);
        $count = 0;

        foreach ($bookings as $booking) {
            $details = $this->bookingDetailRepository->findByBookingId($booking->id);
            $count += count($details);
        }

        return $count;
    }

    public function createShowTime(ShowtimeRequest $showtimeRequest)
    {
        $movie = Movie::findOrFail($showtimeRequest->movieid);
        $room = Room::findOrFail($showtimeRequest->roomid);
        $showtime = ShowTime::create([
            'movieid' => $movie->id,
            'roomid' => $room->id,
            'showdate' => $showtimeRequest->showdate,
            'starttime' => $showtimeRequest->starttime,
            'price' => $showtimeRequest->price,
            'isactive' => true,
        ]);

        Log::info("📅 Đã tạo suất chiếu mới cho phim {$movie->name} tại phòng {$room->name}");
        return $showtime;
    }

    public function getShowtimesByCinemaIdAndDate(int $cinemaId, Carbon $showDate){
        $showtimes = $this->showTimeRepository->findByCinemaIdAndShowdate($cinemaId,$showDate);
        return ShowTimeResource::collection($showtimes);
    }

}
