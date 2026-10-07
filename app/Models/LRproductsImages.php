<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRproductsImages extends Model
{
    use HasFactory;
    protected $table = 'LRproductsImages';

    protected $fillable = [
           'id', 'product_id', 'name', 'status', 'updated_by', 'created_by', 'updated_at', 'created_at'
        ];
}
