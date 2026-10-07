<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRparts extends Model
{
    use HasFactory;
    protected $table = 'LRparts';

    protected $fillable = [
         'id', 'status', 'name', 'part_value', 'parent_id', 'updated_by', 'updated_at', 'created_by', 'created_at' 
    ];

}
