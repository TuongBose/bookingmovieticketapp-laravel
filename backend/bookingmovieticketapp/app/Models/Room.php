<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';

    // Khóa chính
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'cinemaid',
        'name',
        'seatcolumnmax',
        'seatrowmax'
    ];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class, 'cinemaid');
    }
    public function seats()
    {
        return $this->hasMany(Seat::class, 'roomid');
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'roomid');
    }
}
