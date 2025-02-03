<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'icon', 'status',
    ];


    // Section Title
    static public function section($slug)
    {
    	$section = Section::where('slug', $slug)
    					->where('status', 1)
    					->first();

    	return $section;
    }
}
