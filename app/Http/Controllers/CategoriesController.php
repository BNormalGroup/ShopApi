<?php

namespace App\Http\Controllers;
use App\Http\Requests\Categories\StoreRequest;
use App\Http\Requests\Categories\UpdateRequest;
use App\Models\Categories;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete', 'show']]);
    }

    public function index()
    {
        $categories = Categories::where('parent_id', null)->orderByDesc('id')->get();
        return response()->json($categories, 200);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $category = Categories::create($data);
        return response()->json($category, 200);
    }
    public function show($id)
    {
        $category = Categories::where('id', $id)->first();
        if ($category != null) {
            return response()->json($category, 200);
        } else {
            return response()->json(['message' => '404'], 404);
        }
    }
    public function update(UpdateRequest $request, Categories $category)
    {
        $data = $request->validated();
        $category->update($data);
        return response()->json($category, 200);
    }
    public function delete(Categories $category)
    {
        $category->delete();
        return response()->json(['message'=>'done'],200);
    }
}
