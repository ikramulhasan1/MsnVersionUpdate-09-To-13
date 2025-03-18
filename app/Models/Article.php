<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    // Relationship with Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    protected $fillable = [
        'category_id', 'title', 'short_title', 'keywords', 'slug', 'service_id', 'placeholder', 'description', 'image_path', 'video_id', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }
}
