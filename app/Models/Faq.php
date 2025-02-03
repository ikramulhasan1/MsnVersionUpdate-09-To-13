<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'status',
    ];

    public function category()
    {
    	return $this->belongsTo(FaqCategory::class, 'category_id');
    }
}
