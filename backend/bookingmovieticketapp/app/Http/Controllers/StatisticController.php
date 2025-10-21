<?php

namespace App\Http\Controllers;

use App\Services\Booking\IBookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    protected $bookingService;

    public function __construct(IBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function getMonthlyStatistics()
    {
        try {
            $now = Carbon::now();
            $month = $now->month;
            $year = $now->year;

            $statistics = [
                'mostBookedMovie'        => $this->bookingService->getMostBookedMovie($month, $year),
                'secondMostBookedMovie'  => $this->bookingService->getSecondMostBookedMovie($month, $year),
                'thirdMostBookedMovie'   => $this->bookingService->getThirdMostBookedMovie($month, $year),
                'totalRevenue'           => $this->bookingService->calculateMonthlyRevenue($month, $year),
                'mostBookedCinema'       => $this->bookingService->getMostBookedCinema($month, $year),
            ];

            return response()->json($statistics);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
