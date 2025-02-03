<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'image_path', 'video_id', 'mission_title', 'mission_desc', 'vision_title', 'vision_desc', 'status',
    ];
}
