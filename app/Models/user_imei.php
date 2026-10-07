<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class user_imei extends Model
{
    use HasFactory;

    protected $table = 'user_imei';


    protected $fillable = [
        'id', 'device_name', 'user_id', 'imei', 'imei_id', 'server_key', 'fcm_reg_id', 'updated_at','created_at'
    ];
}
