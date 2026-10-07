<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table = 'user_permission';

    protected $fillable = [
        'id', 'user_id', 'all_select', 'all_special_delete', 'allowed_menus', 'created_at', 'created_by', 'updated_by', 'updated_at'
    ];
}
