<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'LRSales_orders';
    protected $guarded = [];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function vendor()
    {
        return $this->belongsTo(LRvendor::class, 'vendor_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'order_id');
    }
}
