<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'location',
        'latitude',
        'longitude',
        'meeting_time',
        'date',
        'city',
        'ip',
        'distance_km',
        'distance_time' 
    ];
}
 