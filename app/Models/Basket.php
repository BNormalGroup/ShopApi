<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;
    protected $table = 'basket';
    protected $fillable = ['user_id', 'item_id', 'colour','size','quantity'];

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }
}
