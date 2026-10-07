<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRticketsAssignLog extends Model
{
    use HasFactory;
    protected $table = 'LRticketsAssignLog';

    protected $fillable = [
        'id', 'userid',  'ticketid', 'solution', 'priority', 'assigned_by', 'assigned_at', 'status', 'updated_by', 'updated_at', 'created_by', 'created_at', 'active'
    ];

}
