<?php

namespace App\Http\Controllers;

use App\Models\ItemColor;

class ItemColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function delete(ItemColor $color)
    {
        $color->delete();
        return response()->json(['message' => 'Done'], 200);
    }
}
