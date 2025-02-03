<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'department', 'description', 'status',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'designation_id', 'id');
    }
}
