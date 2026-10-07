<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class device_parts extends Model
{
    use HasFactory;
    protected $table = 'device_parts';

    protected $fillable = [
        'id', 
        'part', 
        'name',
        'parent_id',
        'model_number',
        'status',
        'updated_by',
        'created_by',
        'updated_at',
        'created_at',
    ];
}
