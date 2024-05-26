<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Items;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
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
        $baskets = Basket::where('user_id', $id)->with('item')->get();

        $itemsAll = $baskets->map(function($basket) {
            return [
                'id' => $basket->id,
                'product' => [
                    'id' => $basket->item->id,
                    'image' => $basket->item->image,
                    'name' => $basket->item->name,
                    'description' => $basket->item->description,
                    'texture' => $basket->item->texture,
                    'price' => $basket->item->price,
                    'category_id' => $basket->item->category_id,
                    'sex' => $basket->item->sex,
                ],
                'color' => $basket->colour,
                'quantity' => $basket->quantity,
                'sizes' => $basket->item->sizes->pluck('size'),
                'selectedSize' => $basket->size,
            ];
        });

        return response()->json($itemsAll, 200);
    }

    public function delete($id)
    {
        Basket::where('id',$id)->first()->delete();
        return response()->json(['message'=>'done'],200);
    }
}
