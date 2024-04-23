<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'brand_id', 'category_id', 'sex', 'image', 'texture'];
    public function likes()
    {
        return $this->hasMany('App\Models\Likes', 'item_id');
    }
}
