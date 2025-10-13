<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    protected $table = 'movies';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'name',
        'description',
        'duration',
        'releasedate',
        'posterurl',
        'bannerurl',
        'agerating',
        'voteaverage',
        'director'
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movieid');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'movieid');
    }

    public function casts()
    {
        return $this->hasMany(Cast::class, 'movieid');
    }
}
