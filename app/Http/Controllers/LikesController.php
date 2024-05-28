<?php

namespace App\Http\Controllers;

use App\Http\Requests\Likes\StoreRequest;
use App\Http\Requests\Likes\UpdateRequest;
use App\Models\ImagesItem;
use App\Models\ItemColor;
use App\Models\Items;
use App\Models\ItemSize;
use App\Models\Likes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Items $item)
    {
        $likesCount = $item->likes()->count();
        return response()->json(['likes_count' => $likesCount], 200);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $like = Likes::create($data);
        return response()->json($like, 200);
    }

    public function show($userId)
    {
        $dataLikes = Likes::where('user_id', $userId)->get();
        $productIds = $dataLikes->pluck('item_id');
        $items = Items::whereIn('id', $productIds)->get();
        $images = ImagesItem::whereIn('item_id', $productIds)->get();
        $colors = ItemColor::whereIn('item_id', $productIds)->get();
        $sizes = ItemSize::whereIn('item_id', $productIds)->get();
        $result = [];
        // Формуємо масив з товарами та додатковими даними
        foreach ($items as $product) {
            $productImages = array_values($images->where('item_id', $product->id)->toArray());
            $productColors = array_values($colors->where('item_id', $product->id)->toArray());
            $productSizes = array_values($sizes->where('item_id', $product->id)->toArray());

// Додаємо в результат об'єкт з усіма даними
            $result[] = [
                'product' => $product,
                'images' => $productImages, // Масив зображень
                'colors' => $productColors, // Масив кольорів
                'sizes' => $productSizes, // Масив розмірів
            ];
        }

        return response()->json($result, 200);
    }

    public function update(UpdateRequest $request, Likes $like)
    {
        $data = $request->validated();
        $like->update($data);
        return response()->json($like, 200);
    }

    public function delete(Likes $like)
    {
        $like->delete();
        return response()->json(['message' => 'done'], 200);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'item_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $like = Likes::where('user_id', $request->input('user_id'))
            ->where('item_id', $request->input('item_id'))
            ->first();
        if ($like) {
            return response()->json(['liked' => true, 'like_id' => $like->id], 200);
        } else {
            return response()->json(['liked' => false], 200);
        }
    }
}
