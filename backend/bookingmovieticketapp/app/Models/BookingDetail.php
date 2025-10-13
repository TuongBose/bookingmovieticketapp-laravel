<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;
    protected $table = 'bookingdetails';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'bookingid',
        'seatid',
        'price',
    ];
    protected $casts = [
        'price' => 'integer',
    ];

    
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingid');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seatid');
    }
}
