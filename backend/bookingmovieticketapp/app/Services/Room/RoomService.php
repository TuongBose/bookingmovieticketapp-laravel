<?php

namespace App\Services\Room;

use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Cinema;
use App\Models\Room;
use App\Repositories\Cinema\ICinemaRepository;
use App\Repositories\Room\IRoomRepository;
use Illuminate\Support\Facades\Log;

class RoomService implements IRoomService
{
    protected $cinemaRepository;
    protected $roomRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        ICinemaRepository $cinemaRepository,
        IRoomRepository $roomRepository
    ) {
        $this->cinemaRepository = $cinemaRepository;
        $this->roomRepository = $roomRepository;
    }

    public function generateRoomsForAllCinemas()
    {
        $cinemas = Cinema::all();
        foreach ($cinemas as $cinema) {
            $this->generateRoomsForCinema($cinema);
        }
    }

    private function generateRoomsForCinema(Cinema $cinema)
    {
        $maxRooms = $cinema->maxroom;
        $existingRooms = $this->roomRepository->findByCinema($cinema);
        if (count($existingRooms) >= $maxRooms) {
            Log::info("Rạp {$cinema->name} (ID: {$cinema->id}) đã có đủ số phòng.");
            return;
        }

        for ($i = 1; $i <= $maxRooms; $i++) {
            $roomName = "RAP {$i}";
            if (collect($existingRooms)->contains(fn($r) => $r->name === $roomName)) {
                continue;
            }

            $seatColumnMax = rand(8, 20);
            $seatRowMax = rand(6, 16);

            Room::create([
                'cinemaid' => $cinema->id,
                'name' => $roomName,
                'seatcolumnmax' => $seatColumnMax,
                'seatrowmax' => $seatRowMax,
            ]);

            Log::info("Tạo phòng {$roomName} cho rạp {$cinema->name} (ID: {$cinema->id})");
        }
    }

    public function createRoom(RoomRequest $roomRequest)
    {
        $existingCinema = Cinema::findOrFail($roomRequest->id);

        $newRoom = Room::create([
            'cinemaid' => $existingCinema->id,
            'name' => $roomRequest->name,
            'seatcolumnmax' => $roomRequest->seatcolumnmax,
            'seatrowmax' => $roomRequest->seatrowmax,
        ]);
        return $newRoom;
    }

    public function getRoomById(int $id)
    {
        $existingRoom = Room::findOrFail($id);
        return new RoomResource($existingRoom);
    }

    public function updateRoom(int $id, RoomRequest $roomRequest)
    {
        $existingRoom = Room::findOrFail($id);
        $existingCinema = Cinema::findOrFail($roomRequest->id);

        $existingRoom->update([
            'cinemaid' => $existingCinema->id,
            'name' => $roomRequest->name,
            'seatcolumnmax' => $roomRequest->seatcolumnmax,
            'seatrowmax' => $roomRequest->seatrowmax,
        ]);

        return $existingRoom;
    }

    public function getRoomByCinemaId(int $cinemaId)
    {
        $existingCinema = Cinema::findOrFail($cinemaId);
        $rooms = $this->roomRepository->findByCinema($existingCinema);
        return RoomResource::collection($rooms);
    }

    public function getAllRooms()
    {
        $rooms = Room::all();
        return RoomResource::collection($rooms);
    }

    public function deleteRoom(int $id)
    {
        Room::destroy($id);
    }
}
