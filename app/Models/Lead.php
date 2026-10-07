<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'LRSales_leads';
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(LRvendor::class, 'vendor_id');
    }

    public function customer()
    {
        return $this->belongsTo(LRvendor::class, 'vendor_id');
    }

    public function salesExecutive()
    {
        return $this->belongsTo(User::class, 'sales_executive_id');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class, 'lead_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'lead_id');
    }
}
