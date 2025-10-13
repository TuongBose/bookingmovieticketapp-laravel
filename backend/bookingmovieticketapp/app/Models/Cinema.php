<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;
    protected $table = 'cinemas';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'city',
        'coordinates',
        'address',
        'phonenumber',
        'maxroom',
        'imagename',
        'isactive',
    ];
    
    public function rooms()
    {
        return $this->hasMany(Room::class, 'cinemaid');
    }
}
