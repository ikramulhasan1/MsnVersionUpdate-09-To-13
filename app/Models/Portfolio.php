<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Subservice;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function subservices()
    {
        return $this->belongsToMany(Subservice::class, 'portfolio_subservice');
    }

    public function categories()
    {
        return $this->belongsToMany(PortfolioCategory::class, 'portfolio_category');
    }
    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }
}
