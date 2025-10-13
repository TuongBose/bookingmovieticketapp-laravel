<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;
    protected $table = 'casts';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'movieid',
        'actorname',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movieid');
    }
}
