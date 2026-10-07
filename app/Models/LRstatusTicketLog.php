<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRstatusTicketLog extends Model
{
    use HasFactory;
    protected $table = 'LRstatusTicketLog';

    protected $fillable = [
        'id', 'name', 'status', 'updated_by', 'updated_at', 'created_at', 'created_by'
    ];

    protected $guarded = [];

}
