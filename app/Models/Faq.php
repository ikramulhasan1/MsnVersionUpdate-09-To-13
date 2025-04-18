<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'service_id', 'title', 'slug', 'description', 'type', 'status',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function category()
    {
    	return $this->belongsTo(FaqCategory::class, 'category_id');
    }
}
