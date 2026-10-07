<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imei_registration extends Model
{
    use HasFactory;
    protected $table = 'imei_registration';

    protected $fillable = [
        'id', 'device_name', 'imei', 'note', 'status', 'updated_by', 'updated_at', 'created_by', 'created_at', 'log_days'
    ];
}
