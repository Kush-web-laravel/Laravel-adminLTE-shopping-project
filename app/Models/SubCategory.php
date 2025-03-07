<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "sub_category";
    protected $fillable = [
        'name',
        'price',
        'parent_id'
    ];

    protected $dates = ['deleted_at'];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
