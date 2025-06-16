<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['adders'])->get();
        return successResponse([
            'categories'  => CategoryResource::collection($categories),
        ]);
        
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $category = Category::create($data);

        if (!empty($data['adders'])) {
            $category->adders()->sync($data['adders']);
        }
        return successResponse([
            'categories'  => new CategoryResource($category),
        ]);
    }

    public function show(Category $category)
    {
        $category->load(['adders']);
        return successResponse([
            'categories'  => new CategoryResource($category),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        $category->update($data);

        if (array_key_exists('adders', $data)) {
            $category->adders()->sync($data['adders']);
        }
        return successResponse([
            'categories'  => new CategoryResource($category),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return successResponse([
            'result'  => null,
        ]);
    }
}
