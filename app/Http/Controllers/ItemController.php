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
        $this->middleware('auth:api', ['except' => ['index', 'store', 'update', 'delete', 'show', 'DeleteImage']]);
    }

    public function index()
    {
        $items = Items::get();
        return response()->json($items, 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        if (!$request->has('images')) {
            return response()->json(['message' => 'Missing file'], 422);
        }
        $item = Items::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sex' => $request->sex,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id
        ]);

        $dir = $_SERVER['DOCUMENT_ROOT'];
        $year = date('Y');
        $month = date('m');
        $basePath = $dir . '/uploads/' . $year . '/' . $month;

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }
        foreach ($input["images"] as $index => $image) {
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($basePath, $filename);
            ImagesItem::create([
                'item_id' => $item->id,
                'url' => $year . '/' . $month . '/' . $filename
            ]);
        }

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
        if ($request->has("images")) {
            // Запис та добавлення нових фото
            $dir = $_SERVER['DOCUMENT_ROOT'];
            $year = date('Y');
            $month = date('m');
            $basePath = $dir . '/uploads/' . $year . '/' . $month;

            if (!file_exists($basePath)) {
                mkdir($basePath, 0777, true);
            }
            foreach ($request["images"] as $index => $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($basePath, $filename);
                ImagesItem::create([
                    'item_id' => $item->id,
                    'url' => $year . '/' . $month . '/' . $filename
                ]);
            }
        }
        $item->update($validatedData);

        return response()->json($item);
    }

    public function DeleteImage(ImagesItem $image)
    {
        // Визначаємо шлях до файлу на основі URL-адреси
        $imagePath = 'uploads/' . $image->url;
        // Перевіряємо, чи файл існує перед його видаленням
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        // Тепер видаляємо записи з бази даних
        $image->delete();
        return response()->json(['message'=>'done'],200);
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
        ////////////////////////////////////////
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

        DB::table('items')
            ->where('id', $id)
            ->delete();
        return response()->json(['message' => 'Done'], 200);
    }
}
