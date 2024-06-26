<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemColor extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'image', 'name','item_id'];
}
