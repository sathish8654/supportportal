<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $table = 'LRSales_installations';
    protected $guarded = [];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function serviceEngineer()
    {
        return $this->belongsTo(User::class, 'service_engineer_id');
    }
}
