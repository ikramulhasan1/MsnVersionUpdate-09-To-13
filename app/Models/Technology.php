<?php

namespace App\Models;

use App\Models\Service;
use App\Models\CaseStudy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Technology extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function caseStudies()
    {
        return $this->belongsToMany(CaseStudy::class);
    }
}
