<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subservice extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function portfolios()
    {
        return $this->belongsToMany(Portfolio::class, 'portfolio_subservice');
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'subservice_technology');
    }

}
