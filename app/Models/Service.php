<?php

namespace App\Models;

use App\Models\Faq;
use App\Models\Whywe;
use App\Models\Industry;
use App\Models\CaseStudy;
use App\Models\Subservice;
use App\Models\Processwork;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'keywords', 'price', 'starting_price', 'priceCurrency', 'average_rating', 'review_count', 'short_title', 'meta_title', 'slug', 'short_desc', 'description', 'image_path', 'file_path', 'manu', 'status',
    ];

    public function caseStudies()
    {
        return $this->belongsToMany(CaseStudy::class);
    }
    // Relationship with Article
    public function articles()
    {
        return $this->hasMany(Article::class, 'service_id');
    }
    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }
    public function processworks()
    {
        return $this->hasMany( Processwork::class);
    }
    public function industries()
    {
        return $this->hasMany( Industry::class);
    }
    public function whywes()
    {
        return $this->hasMany( Whywe::class);
    }
    public function subservices()
    {
        return $this->hasMany(Subservice::class);
    }
    public function technologies()
    {
        return $this->hasMany(Technology::class);
    }
    // Polymorphic relations
    public function quotes()
    {
        return $this->morphedByMany(GetQuote::class, 'serviceable');
    }

    public function invoices()
    {
        return $this->morphedByMany(Invoice::class, 'serviceable');
    }
}
