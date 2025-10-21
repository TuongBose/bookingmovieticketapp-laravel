<?php

namespace App\Http\Controllers;

use App\Services\Cinema\ICinemaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CinemaController extends Controller
{
    protected $cinemaService;

    public function __construct(ICinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    public function getCinemaByMovieIdAndCityAndDate(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'movieId' => 'required|integer',
                'city' => 'required|string',
                'date' => 'required|date',
            ])->validate();

            $cinemas = $this->cinemaService->getCinemaByMovieIdAndCityAndDate(
                $validated['movieId'],
                $validated['city'],
                Carbon::parse($validated['date'])
            );

            return response()->json($cinemas);
        } catch (Exception $e) {
            Log::error('Lỗi getCinemaByMovieIdAndCityAndDate: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllCinema()
    {
        return response()->json($this->cinemaService->getAllCinema());
    }

    public function getCinemaById(int $id)
    {
        try {
            $cinema = $this->cinemaService->getCinemaById($id);
            return response()->json($cinema);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function uploadCinemaImage(int $id, Request $request)
    {
        try {
            if (!$request->hasFile('image')) {
                return response()->json(['error' => 'Không có tệp hình ảnh được gửi.'], 400);
            }

            $file = $request->file('image');
            if (!$file->isValid() || !Str::startsWith($file->getMimeType(), 'image/')) {
                return response()->json(['error' => 'Tệp phải là hình ảnh (jpg, png, v.v.).'], 400);
            }

            $cinema = $this->cinemaService->getCinemaById($id);

            // Xóa ảnh cũ nếu có
            if ($cinema->imagename && Storage::disk('public')->exists('cinemas/' . $cinema->imagename)) {
                Storage::disk('public')->delete('cinemas/' . $cinema->imagename);
            }

            // Lưu ảnh mới
            $fileName = 'cinema_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('cinemas', $fileName, 'public');

            $cinema->imagename = $fileName;
            $this->cinemaService->saveCinema($cinema);

            return response()->json(['message' => 'Tải lên hình ảnh thành công.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Lỗi khi lưu hình ảnh: ' . $e->getMessage()], 400);
        }
    }

    public function getCinemaImage(int $id)
    {
        try {
            $cinema = $this->cinemaService->getCinemaById($id);

            if (!$cinema || !$cinema->imagename) {
                return response()->json(['error' => 'Không có hình ảnh cho rạp này.'], 404);
            }

            $path = storage_path('app/public/cinemas/' . $cinema->imagename);
            if (!file_exists($path)) {
                return response()->json(['error' => 'File hình không tồn tại.'], 404);
            }

            return response()->file($path);
        } catch (Exception $e) {
            return response()->json(['error' => 'Lỗi khi tải hình ảnh: ' . $e->getMessage()], 400);
        }
    }

    public function createCinema(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required|string',
                'city' => 'required|string',
                'coordinates' => 'required|string|regex:/^-?\d+\.\d+,\s*-?\d+\.\d+$/',
                'address' => 'required|string',
                'phonenumber' => 'required|regex:/^0\d{9,10}$/',
                'maxroom' => 'required|integer|min:1',
                'image' => 'nullable|image'
            ])->validate();

            $cinema = $this->cinemaService->createCinema((object) $validated);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = 'cinema_' . $cinema->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('cinemas', $fileName, 'public');
                $cinema->imagename = $fileName;
                $this->cinemaService->saveCinema($cinema);
            }

            return response()->json($cinema);
        } catch (Exception $e) {
            return response()->json(['error' => 'Lỗi khi tạo cinema: ' . $e->getMessage()], 400);
        }
    }

    public function updateCinema(int $id, Request $request)
    {
        try {
            $cinemaDTO = (object) $request->all();
            $cinema = $this->cinemaService->updateCinema($id, $cinemaDTO);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = 'cinema_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('cinemas', $fileName, 'public');
                $cinema->imagename = $fileName;
                $this->cinemaService->saveCinema($cinema);
            }

            return response()->json($cinema);
        } catch (Exception $e) {
            return response()->json(['error' => 'Lỗi khi cập nhật cinema: ' . $e->getMessage()], 400);
        }
    }

    public function updateCinemaStatus(int $id, Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'isActive' => 'required|boolean'
            ])->validate();

            $this->cinemaService->updateCinemaStatus($id, $validated['isActive']);
            return response()->json(['message' => 'Cập nhật trạng thái cinema thành công.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
