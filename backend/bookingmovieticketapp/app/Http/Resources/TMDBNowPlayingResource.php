<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TMDBNowPlayingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'dates' => [
                'maximum' => $this->dates['maximum'] ?? null,
                'minimum' => $this->dates['minimum'] ?? null,
            ],
            'results' => TMDBMovieResource::collection(collect($this->results ?? [])),
        ];
    }
}
