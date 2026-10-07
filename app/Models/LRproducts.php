<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRproducts extends Model
{
    use HasFactory;
    protected $table = 'LRproducts';

    protected $fillable = [
         'id', 'batch_id', 'status', 'category', 'model', 'model_id', 'vendor_id', 'serial', 'note', 'updated_by', 'updated_at', 'created_by', 'created_at'
    ];

}
