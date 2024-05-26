<?php

namespace App\Http\Controllers;

use App\Models\ItemSize;

class ItemSizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function delete(ItemSize $size)
    {
        $size->delete();
        return response()->json(['message' => 'Done'], 200);
    }
}
