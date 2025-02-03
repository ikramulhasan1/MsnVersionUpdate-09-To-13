<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'designation_id', 'title', 'slug', 'description', 'image_path', 'facebook', 'twitter', 'instagram', 'linkedin', 'email', 'phone', 'whatsapp', 'website', 'status',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
