<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $table = 'users';
protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phonenumber',
        'address',
        'dateofbirth',
        'imagename',
        'createdat',
        'isactive',
        'rolename'
    ];

    protected $casts = [
        'isactive' => 'boolean',
        'rolename' => 'boolean',
        'dateofbirth' => 'date',
        'createdat' => 'datetime'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'userid');
    }

    // Một user có thể có nhiều rating phim
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'userid');
    }
}
