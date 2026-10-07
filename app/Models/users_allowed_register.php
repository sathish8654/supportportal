<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_allowed_register extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
    ];
}
