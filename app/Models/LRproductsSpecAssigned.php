<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRproductsSpecAssigned extends Model
{
    use HasFactory;
    protected $table = 'LRproductsSpecAssigned';

    protected $fillable = [
           'product_id', 'spec_id', 'spec_number_id', 'spec_name', 'spec_number', 'updated_by', 'updated_at', 'created_by', 'created_at'
        ];
}
