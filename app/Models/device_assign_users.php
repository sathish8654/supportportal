<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class device_assign_users extends Model
{
    use HasFactory;
    protected $table = 'device_assign_users';

    protected $fillable = [
        'id', 
        'username', 
        'mobile',
        'email',
        'imei',
        'village',
        'city',
        'status',
        'updated_by',
        'created_by',
        'updated_at',
        'created_at',
    ];
}
