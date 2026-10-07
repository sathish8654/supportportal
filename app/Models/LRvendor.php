<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRvendor extends Model
{
    use HasFactory;
    protected $table = 'LRvendor';

    protected $fillable = [
        'id', 'status', 'name', 'companyname', 'created_at', 'address', 'mobile1', 'mobile2', 'city', 'district', 'state', 'pincode', 'gstno', 'imageUrl','goe_location','remark', 'updated_by', 'updated_at', 'created_by',
    ];

}
