<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LRcategory extends Model
{
    use HasFactory;
    protected $table = 'LRcategory';

    protected $fillable = [
        'id', 'name', 'parent_id', 'model_number', 'status', 'updated_by', 'updated_at', 'created_at', 'created_by'
    ];

    protected $guarded = [];
    public function subcategories(){
        return $this->hasMany('App\Models\LRcategory', 'parent_id');
    }



    public function parent()
    {
        return $this->belongsTo(static::class,'parent_id');
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')
                    ->orderBy('name', 'asc');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }


}
