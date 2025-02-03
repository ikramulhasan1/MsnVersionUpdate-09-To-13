<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'short_desc', 'description', 'image_path', 'file_path', 'status',
    ];


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
