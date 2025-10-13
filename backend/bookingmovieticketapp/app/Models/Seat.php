<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;
    protected $table = 'seats';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'roomid',
        'seatnumber',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'roomid');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'seatid');
    }
}
