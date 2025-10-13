<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'bookingid',
        'totalprice',
        'paymentmethod',
        'paymentstatus',
        'paymenttime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingid');
    }
}
