<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRparts_replaced extends Model
{
    use HasFactory;
    protected $table = 'LRparts_replaced';

    protected $fillable = [
        'id', 'ticket_id', 'part_id', 'part_number_id', 'part_name', 'part_number', 'part_value', 'mode', 'updated_by', 'updated_at', 'created_by', 'created_at' 
    ];

}
