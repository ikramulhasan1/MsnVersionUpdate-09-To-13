<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'icon', 'status',
    ];


    // Email Template
    static public function template($slug)
    {
    	$template = EmailTemplate::where('slug', $slug)
    					->where('status', 1)
    					->first();

    	return $template;
    }
}
