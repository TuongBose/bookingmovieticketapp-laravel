<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowTime extends Model
{
    use HasFactory;
    protected $table = 'showtimes';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'movieid',
        'roomid',
        'showdate',
        'starttime',
        'price',
        'isactive',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movieid');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'roomid');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'showtimeid');
    }
}
