<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryOrders extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'user_id', 'color_id', 'size_id', 'status_id'];

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
}
