<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagesItem extends Model
{
    use HasFactory;
    protected $table = 'images';
    protected $fillable = ['item_id', 'url'];

}
