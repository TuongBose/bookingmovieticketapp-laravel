<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $table = 'ratings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'movieid',
        'userid',
        'rating',
        'comment',
        'createdat'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movieid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
