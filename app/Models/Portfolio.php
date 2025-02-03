<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'image_path', 'video_id', 'link', 'status',
    ];

    public function categories()
    {
        return $this->belongsToMany(PortfolioCategory::class, 'portfolio_category');
    }
}
