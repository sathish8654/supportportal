<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'LRSales_quotations';
    protected $guarded = [];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function vendor()
    {
        return $this->belongsTo(LRvendor::class, 'vendor_id');
    }

    public function salesExecutive()
    {
        return $this->belongsTo(User::class, 'sales_executive_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}
