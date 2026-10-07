<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRiotSim_payment_history extends Model
{
    use HasFactory;

    protected $table = 'LRiotSim_payment_history';

    protected $fillable = [
        'id', 'sim_phone_number', 'usercontact_number', 'paid_date', 'from_date', 'to_date', 'cost', 'transaction_id', 'note','mode','status', 'updated_by', 'updated_at', 'created_by', 'created_at' 
    ];

    
}