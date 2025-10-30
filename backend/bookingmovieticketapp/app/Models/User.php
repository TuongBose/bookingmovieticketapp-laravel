<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
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
        'createdat', //can thiet cho viec luu tru
        'isactive',
        'rolename'
    ];

    protected $hidden = [
        'password',
        'remember_token', 
    ];

    protected $casts = [
        'isactive' => 'boolean',
        'rolename' => 'boolean',
        'dateofbirth' => 'date',
        'createdat' => 'datetime',
        //'password' => 'hashed', // Tự động mã hóa
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
