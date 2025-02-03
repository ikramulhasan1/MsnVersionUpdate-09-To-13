<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quote_id', 'name', 'email', 'phone', 'address', 'city', 'company', 'total_amount', 'discount_amount', 'invoice_amount', 'service_charge', 'tax', 'shipping', 'invoice_date', 'due_date', 'message', 'terms_conditions', 'reference', 'attach', 'invoice_type', 'estimate_flag', 'status',
    ];


    // Polymorphic relations
    public function services()
    {
        return $this->morphToMany(Service::class, 'serviceable');
    }

    public function quote()
    {
        return $this->belongsTo(GetQuote::class, 'quote_id');
    }
}
