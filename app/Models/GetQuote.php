<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetQuote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'company', 'website', 'prefer_contact', 'quantity', 'message', 'file_path', 'pre_delivery_time', 'where_find', 'amount', 'invoice_time', 'mail_status', 'status',
    ];


    // Polymorphic relations
    public function services()
    {
        return $this->morphToMany(Service::class, 'serviceable');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'quote_id', 'id');
    }
}
