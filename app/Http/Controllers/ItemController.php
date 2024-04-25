<?php

namespace App\Http\Controllers;

use App\Models\ImagesItem;
use App\Models\ItemColor;
use App\Models\Items;
use App\Models\ItemSize;
use Illuminate\Http\Request;
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
        if (!$request->has('images') || !$request->has('image')) {
            return response()->json(['message' => 'Missing file'], 422);
        }
        $dir = $_SERVER['DOCUMENT_ROOT'];
        $year = date('Y');
        $month = date('m');
        $basePath = $dir . '/uploads/' . $year . '/' . $month;

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }
        $imageMain = $request->image;
        $mainFileName = uniqid() . '.' . $imageMain->getClientOriginalExtension();
        $imageMain->move($basePath, $mainFileName);

        $sex = ($request->sex === 'man' || $request->sex === 'woman') ? $request->sex : 'unisex';

        $item = Items::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sex' => $sex,
            'texture' => $request->texture,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'image' => $year . '/' . $month . '/' . $mainFileName
        ]);

        foreach ($request['sizes'] as $size)
        {
            ItemSize::create([
                'item_id' => $item->id,
                'size' => $size['size']
            ]);
        }
        foreach ($request['colors'] as $color)
        {
            $imageColor = $color['image'];
            $fileNameColor = uniqid() . '.' . $imageColor->getClientOriginalExtension();
            $imageColor->move($basePath, $fileNameColor);
            ItemColor::create([
                'item_id' => $item->id,
                'image' => $year . '/' . $month . '/' . $fileNameColor,
                'name' => $color['name']
            ]);
        }

        foreach ($request["images"] as $image) {
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($basePath, $filename);
            ImagesItem::create([
                'item_id' => $item->id,
                'url' => $year . '/' . $month . '/' . $filename
            ]);
        }

        return response()->json($item, 200);
    }
    //@todo make update with new data
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

    public function show($id)
    {
        $product = Items::where('id', $id)->first();
        $images = ImagesItem::where('item_id', $id)->get();
        $sizes = ItemSize::where('item_id', $id)->get();
        $colors = ItemColor::where('item_id', $id)->get();

        if ($product != null) {
            return response()->json([
                'status' => 200,
                'product' => $product,
                'images' => $images,
                'sizes' => $sizes,
                'colors' => $colors
            ]);
        } else {
            return response()->json(['message' => '404'], 404);
        }
    }
//@todo make delete with image/sizes/colors
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
        DB::table('item_colors')->where('item_id', $id)->delete();
        DB::table('item_sizes')->where('item_id', $id)->delete();
        DB::table('items')
            ->where('id', $id)
            ->delete();
        return response()->json(['message' => 'Done'], 200);
    }
}
