<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LR_Category extends Model
{
    use HasFactory;
    protected $table = 'LR_Category';

    protected $fillable = [
        'id', 'name', 'parent_id', 'updated_by', 'updated_at', 'created_at', 'created_by'
    ];

    protected $guarded = [];
    public function subcategories(){
        return $this->hasMany('App\Models\LR_Category', 'parent_id');
    }
}
