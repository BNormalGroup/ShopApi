<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Items;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function store(Request $request)
    {
        $basket = Basket::create([
            'user_id' => $request->user_id,
            'item_id' => $request->item_id,
            'colour' => $request->colour,
            'size' => $request->size,
            'quantity' => $request->quantity
        ]);
        return $basket;
    }

    public function show($id)
    {
        $itemsAll = [];
        $itemsId= Basket::where('user_id', $id)->get();

        foreach ($itemsId as $item) {
            $item = Items::where('id', $item['item_id'])->first();
            $itemsAll[] = $item;
        }

        return response()->json([
            'items' => $itemsAll,
        ],200);
    }

    public function delete($id)
    {
        Basket::where('id',$id)->first()->delete();
        return response()->json(['message'=>'done'],200);
    }
}
