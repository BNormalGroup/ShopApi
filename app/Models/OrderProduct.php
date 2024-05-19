<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'item_id', 'color','size','quantity'];

    public function getColorAttribute($value)
    {
        return ['name' => $value];
    }

    // Аксесуар для 'size'
    public function getSizeAttribute($value)
    {
        return ['size' => $value];
    }
    public function item()
    {
        return $this->belongsTo(Items::class);
    }
}
