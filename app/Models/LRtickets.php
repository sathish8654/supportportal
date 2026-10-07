<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRtickets extends Model
{
    use HasFactory;
    protected $table = 'LRtickets';

    protected $fillable = [
        'id', 'vendorid', 'case', 'categoryid', 'category', 'model', 'serial', 'note', 'solution', 'cost', 'status', 'delivery_status', 'delivery_mode', 'delivery_handover', 'delivery_contact', 'delivery_note', 'updated_by', 'updated_at', 'created_by', 'created_at'
    ];

}
