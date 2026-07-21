<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'phone',
    //     'location',
    //     'latitude',
    //     'longitude',
    //     'meeting_time',
    //     'date',
    //     'city',
    //     'ip',
    //     'distance_km',
    //     'distance_time',
    // ];

    protected $guarded = [];
}
