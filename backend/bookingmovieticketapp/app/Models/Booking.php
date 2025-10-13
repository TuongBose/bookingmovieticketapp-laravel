<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = "bookings";
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = [
        'userid',
        'showtimeid',
        'bookingdate',
        'totalprice',
        'paymentmethod',
        'paymentstatus',
        'isactive'
    ];
    protected $casts = [
        'bookingdate'=> 'datetime',
        'isactive'=> 'boolean',
        'totalprice'=>'integer'
    ];

    public function user(){
        return $this->belongsTo(User::class,'userid');
    }
    
    public function showtime(){
        return $this->belongsTo(ShowTime::class,'showtimeid');
    }

    public function bookingdetail(){
    return $this->hasMany(BookingDetail::class,'bookingid');
    }

    public function payment(){
    return $this->hasOne(Payment::class,'bookingid');
    }
}
