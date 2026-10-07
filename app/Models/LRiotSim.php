<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRiotSim extends Model
{
    use HasFactory;

    protected $table = 'LRiotSim';

    protected $fillable = [
        'id',
        'simStatus',
        'renew',
        'simNo',
        'simPhoneNumber',
        'activateDate',
        'custName',
        'custAadharName',
        'custContactNumber',
        'custAadharNumber',
        'custAddress',
        'imei',
        'unitSerialNumber',
        'kycStatus',
        'payment',
        'cost',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'renewalDate',
        'careOff',
        'note',
        'simOperator',
    ];

    
}