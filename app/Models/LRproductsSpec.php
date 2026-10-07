<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRproductsSpec extends Model
{
    use HasFactory;
    protected $table = 'LRproductsSpec';

    protected $fillable = [
           'id', 'name', 'parent_id', 'model_number', 'status', 'updated_by', 'created_by', 'updated_at', 'created_at'
        ];
}
