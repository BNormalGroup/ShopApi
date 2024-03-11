<?php

namespace App\Http\Controllers;

use App\Models\ImagesItem;
use App\Models\Items;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete','show']]);
    }
    public function index()
    {
        $items = Items::get();
        return response()->json($items, 200);
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
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|int',
            'color' => 'required|string',
            'sex' => 'required|string',
            'category_id' => 'required|int',
            'brand_id' => 'required|int'
        ]);
        $item = Items::findOrFail($id);
        if ($request->has("image")) {
            if ($item["image"] != null && $item["image"] != "") {
                $imagePath = public_path('uploads/' . $item->image);

                if (Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                } elseif (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $filename = uniqid() . '.' . $request->file("image")->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads'), $filename);
            $validatedData["image"] = $filename;
        }
        $item->update($validatedData);

        return response()->json($item);
    }
    public function AddImage(Request $request)
    {
        $input = $request->all();
        if (!$request->has('url')) {
            return response()->json(['message' => 'Missing file'], 422);
        }
        $year = date('Y');
        $month = date('m');
        $basePath = public_path('uploads/' . $year . '/' . $month);
        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }
        $filename = uniqid() . '.' . $request->file("url")->getClientOriginalExtension();
        $request->file('url')->move($basePath, $filename);
        $input["url"] = $year . '/' . $month . '/' . $filename;
        $imageUrl = url($input["url"]);
        $item = ImagesItem::create([
            'item_id' => $request->item_id,
            'url' => $imageUrl
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
    public function delete($id)
    {
        $imagesIds = DB::table('images')
            ->where('item_id', $id)
            ->pluck('id');

        foreach ($imagesIds as $imageIdId) {
            $imageDelete = DB::table('images')
                ->where('id', $imageIdId)
                ->first();

            $imagePath = public_path('uploads/' . $imageDelete->url);

            if (Storage::disk('public')->exists($imageDelete->url)) {
                Storage::disk('public')->delete($imageDelete->url);
            } elseif (file_exists($imagePath)) {
                unlink($imagePath);
            }
            DB::table('images')
                ->where('id', $imageIdId)
                ->delete();
        }
        $DeleteItem = DB::table('items')->where('id', $id)->first();
        $imagePath = public_path('uploads/' . $DeleteItem->image);

        if (Storage::disk('public')->exists($DeleteItem->image)) {
            Storage::disk('public')->delete($DeleteItem->image);
        } elseif (file_exists($imagePath)) {
            unlink($imagePath);
        } else {
            return response()->json(['message' => 'Missing file'], 422);
        }
        DB::table('items')
            ->where('id', $id)
            ->delete();
        return response()->json(['message' => 'Done'], 200);
    }
}
