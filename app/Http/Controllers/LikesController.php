<?php

namespace App\Http\Controllers;

use App\Http\Requests\Likes\StoreRequest;
use App\Http\Requests\Likes\UpdateRequest;
use App\Models\Items;
use App\Models\Likes;
use Illuminate\Http\Request;

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
      $data = Likes::where('userId',$userId)->get();
      return response()->json($data,200);
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
}
