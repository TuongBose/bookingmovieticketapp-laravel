<?php

namespace App\Http\Controllers;

use App\Services\Cast\ICastService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CastController extends Controller
{
    protected $castService;

    public function __construct(ICastService $castService)
    {
        $this->castService = $castService;
    }

    public function getCastByMovieId(int $id)
    {
        try {
            $casts = $this->castService->getCastByMovieId($id);
            return response()->json($casts, 200);
        } catch (Exception $e) {
            Log::error("Lỗi khi lấy cast theo movieId {$id}: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}