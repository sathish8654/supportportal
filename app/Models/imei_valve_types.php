<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imei_valve_types extends Model
{
    use HasFactory;
    protected $table = 'imei_valve_types';

    protected $fillable = [
        'id', 
        'imei_id', 
        'user_id', 
        'name_type',
        'value_qty',
        'status',
    ];
}
