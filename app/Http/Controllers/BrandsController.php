<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brands\StoreRequest;
use App\Http\Requests\Brands\UpdateRequest;
use App\Models\Brand;

class BrandsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete','show']]);
    }

    public function index()
    {
        $brands = Brand::get();
        return response()->json($brands, 200);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $brand = Brand::create($data);
        return response()->json($brand, 200);
    }
    public function show($id)
    {
        $brand = Brand::where('id', $id)->first();
        if ($brand != null) {
            return response()->json($brand, 200);
        } else {
            return response()->json(['message' => '404'], 404);
        }
    }
    public function update(UpdateRequest $request, Brand $brand)
    {
        $data = $request->validated();
        $brand->update($data);
        return response()->json($brand, 200);
    }
    public function delete(Brand $brand)
    {
        $brand->delete();
        return response()->json(['message'=>'done'],200);
    }
}
