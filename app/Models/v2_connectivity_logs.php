<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class v2_connectivity_logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'time', 'connection_status', 'clientip_port', 'imei', 'server_time',
    ];
}
