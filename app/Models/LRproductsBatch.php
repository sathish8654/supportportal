<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRproductsBatch extends Model
{
    use HasFactory;
    protected $table = 'LRproductsBatch';

    protected $fillable = [
         'id', 'status', 'category', 'model', 'model_id', 'note', 'qty', 'assigned', 'updated_by', 'updated_at', 'created_by', 'created_at'
    ];

}
