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
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete', 'show']]);
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
      $dataLikes = Likes::where('user_id',$userId)->get();
      $products = [];
      foreach ($dataLikes as $like)
      {
          $product = Items::where('id', $like->item_id)->first();
          $images = ImagesItem::where('item_id', $like->item_id)->get();
          $sizes = ItemSize::where('item_id', $like->item_id)->get();
          $colors = ItemColor::where('item_id', $like->item_id)->get();

          $productData = [
              'product' => $product,
              'images' => $images,
              'colors' => $colors,
              'sizes' => $sizes,
          ];
          $products[] = $productData;
      }
      return response()->json($products,200);
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
        return response()->json(['message'=>'done'],200);
    }

    public function isLiked (Request $request)
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
            return response()->json(['liked' => true], 200);
        } else {
            return response()->json(['liked' => false], 200);
        }
    }
}
