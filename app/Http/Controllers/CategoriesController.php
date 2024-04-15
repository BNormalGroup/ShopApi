<?php

namespace App\Http\Controllers;
use App\Http\Requests\Categories\StoreRequest;
use App\Http\Requests\Categories\UpdateRequest;
use App\Models\Categories;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete', 'show', 'getCategoriesWithChildren']]);
    }

    public function index()
    {
        $categories = Categories::orderByDesc('id')->get();
        return response()->json($categories, 200);
    }

    public function getCategoriesWithChildren()
    {
        // Отримуємо всі головні категорії
        $categories = Categories::where('parent_id', null)->orderByDesc('id')->get();

        // Для кожної головної категорії викликаємо метод, що рекурсивно отримує її дочірні категорії
        $categoriesWithChildren = $categories->map(function ($category) {
            $category->children = $this->getChildren($category);
            return $category;
        });

        // Повертаємо результат у форматі JSON
        return response()->json($categoriesWithChildren, 200);
    }

    protected function getChildren($category)
    {
        // Отримуємо всі дочірні категорії поточної категорії
        $children = Categories::where('parent_id', $category->id)->get();

        // Для кожної дочірньої категорії викликаємо метод, що рекурсивно отримує її дочірні категорії
        $children->each(function ($childCategory) {
            $childCategory->children = $this->getChildren($childCategory);
        });

        return $children;
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
