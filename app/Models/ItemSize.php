<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSize extends Model
{
    use HasFactory;
    protected $table = 'item_sizes';
    protected $fillable = ["id", "item_id","size"];
}
