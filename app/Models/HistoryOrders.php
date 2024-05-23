<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryOrders extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_address_id', 'user_id', 'status_id'];

    // Відношення до таблиці `Colors`
    public function color()
    {
        return $this->belongsTo(ItemColor::class);
    }

    // Відношення до таблиці `Sizes`
    public function size()
    {
        return $this->belongsTo(ItemSize::class);
    }
    public function user()
    {
        return $this->belongsTo(Users::class);
    }
    public function item()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->with(['item']);
    }
    public function status()
    {
        return $this->belongsTo(OrderStatuses::class);
    }

    public function delivery_address()
    {
        return $this->belongsTo(OrderDeliveryAddress::class);
    }
}
