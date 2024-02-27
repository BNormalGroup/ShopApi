<?php

namespace App\Http\Controllers;

use App\Models\ImagesItem;
use App\Models\Items;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete','show']]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        if (!$request->has('image')) {
            return response()->json(['message' => 'Missing file'], 422);
        }
        $dir = $_SERVER['DOCUMENT_ROOT'];
        $year = date('Y');
        $month = date('m');
        $basePath = $dir . '/uploads/' . $year . '/' . $month;
        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }
        $filename = uniqid() . '.' . $request->file("image")->getClientOriginalExtension();
        $fileSave = $basePath . '/' . $filename;
        ImageHelper::image_resize(700, 700, $fileSave, 'image');
        $input["image"] = $year . '/' . $month . '/' . $filename;

        $item = Items::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'color' => $request->color,
            'sex' => $request->sex,
            'category_id' => $request->category_id,
            'image' => $input["image"],
            'brand_id' => $request->brand_id
        ]);
        return $item;
    }

    public function show($id)
    {
        $product = Items::where('id', $id)->first();
        $images = ImagesItem::where('item_id', $id)->get();
        if ($product != null) {
            return response()->json([
                'status' => 200,
                'items_data' => [
                    'product' => $product,
                    'images' => $images
                ]
            ]);
        } else {
            return response()->json(['message' => '404'], 404);
        }
    }

}
