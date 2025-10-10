<?php

namespace App\Models;

use App\Models\Service;
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
}
