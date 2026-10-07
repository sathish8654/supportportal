<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRstatusSim extends Model
{
    use HasFactory;
    protected $table = 'LRstatusSim';

    protected $fillable = [
        'id', 'name', 'status', 'updated_by', 'updated_at', 'created_at', 'created_by'
    ];

    protected $guarded = [];

}
