<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TMDBUpComingMovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'adult' => $this->adult ?? false,
            'backdrop_path' => $this->backdrop_path ?? null,
            'genre_ids' => $this->genre_ids ?? [],
            'id' => $this->id ?? null,
            'original_language' => $this->original_language ?? null,
            'original_title' => $this->original_title ?? null,
            'overview' => $this->overview ?? null,
            'popularity' => $this->popularity ?? 0.0,
            'poster_path' => $this->poster_path ?? null,
            'release_date' => $this->release_date ?? null,
            'title' => $this->title ?? null,
            'video' => $this->video ?? false,
            'vote_average' => $this->vote_average ?? 0.0,
            'vote_count' => $this->vote_count ?? 0,
        ];
    }
}
