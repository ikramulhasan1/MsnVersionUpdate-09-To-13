<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseStudy extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }

    protected $casts = [
        'case_steps' => 'array',
    ];
}
