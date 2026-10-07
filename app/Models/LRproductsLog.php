<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRproductsLog extends Model
{
    use HasFactory;
    protected $table = 'LRproductsLog';

    protected $fillable = [
          'id', 'batch_id', 'product_id', 'assigned', 'assigned_by', 'assigned_at', 'status', 'note', 'vendor_id', 'active', 'updated_by', 'updated_at', 'created_by', 'created_at'
    ];

}
