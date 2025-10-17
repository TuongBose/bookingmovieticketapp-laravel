<?php

namespace App\Services\Booking;

use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\ShowTime;
use App\Models\User;
use App\Repositories\Booking\IBookingRepository;
use App\Repositories\ShowTime\IShowTimeRepository;
use App\Repositories\User\IUserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService implements IBookingService
{
    protected $bookingRepository;
    protected $showTimeRepository;
    protected $userRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(
        IBookingRepository $bookingRepository,
        IShowTimeRepository $showTimeRepository,
        IUserRepository $userRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->showTimeRepository = $showTimeRepository;
        $this->userRepository = $userRepository;

        $this->updateBookingStatus();
    }

    protected function updateBookingStatus()
    {
        $bookings = Booking::all();
        $now = Carbon::now();

        foreach ($bookings as $booking) {
            $showTime = $booking->showTime;
            if ($showTime && $showTime->starttime) {
                $booking->isactive = $now->lt(Carbon::parse($showTime->starttime));
                $booking->save();
            }
        }
    }

    public function createBooking(BookingRequest $bookingRequest)
    {
        $user = User::findOrFail($bookingRequest->userid);
        $showTime = ShowTime::findOrFail($bookingRequest->showtimeid);

        return Booking::create([
            'userid' => $user->id,
            'showtimeid' => $showTime->id,
            'bookingdate' => Carbon::now(),
            'totalprice' => $bookingRequest->totalprice,
            'paymentmethod' => $bookingRequest->paymentmethod,
            'paymentstatus' => $bookingRequest->paymentstatus,
            'isactive' => true,
        ]);
    }

    public function getBookingByShowTimeId(int $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $bookings = Booking::where('showtimeid', $showTime->id)->get();
        return BookingResource::collection($bookings);
    }

    public function getBookingByUserId(int $id)
    {
        $user = User::findOrFail($id);
        $bookings = Booking::where('userid', $user->id)->get();
        return BookingResource::collection($bookings);

    }

    public function getAllBooking()
    {
        $bookings = Booking::all();
        return BookingResource::collection($bookings);
    }

    public function getBookingById(int $id)
    {
        return Booking::findOrFail($id);
    }

    public function updateBooking(int $id, BookingRequest $bookingRequest)
    {
        $booking = Booking::findOrFail($id);
        $user = User::findOrFail($bookingRequest->userid);
        $showTime = ShowTime::findOrFail($bookingRequest->showtimeid);

        $booking->update([
            'userid' => $user->id,
            'showtimeid' => $showTime->id,
            'totalprice' => $bookingRequest->totalprice,
            'paymentmethod' => $bookingRequest->paymentmethod,
            'paymentstatus' => $bookingRequest->paymentstatus,
        ]);
        return $booking;
    }

    public function deleteBooking(int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['isactive' => false]);
    }

    public function sumTotalPriceByUserId(int $userId)
    {
        return Booking::where('userid', $userId)
            ->whereYear('bookingdate', Carbon::now()->year)
            ->sum('totalprice');
    }

    public function getMostBookedMovie(int $month, int $year)
    {
        return DB::table('bookings')
            ->join('showtimes', 'bookings.showtimeid', '=', 'showtimes.id')
            ->join('movies', 'showtimes.movieid', '=', 'movies.id')
            ->select('movies.id as movieId', 'movies.name as movieName', DB::raw('COUNT(bookings.id) as totalBookings'))
            ->whereMonth('bookings.bookingdate', $month)
            ->whereYear('bookings.bookingdate', $year)
            ->groupBy('movies.id', 'movies.name')
            ->orderByDesc('totalBookings')
            ->first();
    }

    public function calculateMonthlyRevenue(int $month, int $year)
    {
        $startDate = Carbon::create($year, $month, 1, 0, 0, 0);
        $endDate = $startDate->copy()->endOfMonth();

        return $this->bookingRepository->calculateTotalRevenue($startDate, $endDate);
    }

    public function getMostBookedCinema(int $month, int $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $result = $this->bookingRepository->findMostBookedCinema($startDate, $endDate);

        if (empty($result)) {
            return [];
        }

        return [
            'cinemaId' => $result[0]->id,
            'cinemaName' => $result[0]->name,
            'totalBookings' => $result[0]->total_bookings,
        ];
    }

    public function getSecondMostBookedMovie(int $month, int $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $result = $this->bookingRepository->findSecondMostBookedMovie($startDate, $endDate);

        if (empty($result)) {
            return [];
        }

        return [
            'movieId' => $result[0]->id,
            'movieName' => $result[0]->name,
            'totalBookings' => $result[0]->total_bookings,
        ];
    }

    public function getThirdMostBookedMovie(int $month, int $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $result = $this->bookingRepository->findThirdMostBookedMovie($startDate, $endDate);

        if (empty($result)) {
            return [];
        }

        return [
            'movieId' => $result[0]->id,
            'movieName' => $result[0]->name,
            'totalBookings' => $result[0]->total_bookings,
        ];
    }
}
