<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class device_valve_types extends Model
{
    use HasFactory;
    protected $table = 'device_valve_types';

    protected $fillable = [
        'id', 
        'imei_id', 
        'user_id', 
        'name_type',
        'value_qty',
        'status',
    ];
}
