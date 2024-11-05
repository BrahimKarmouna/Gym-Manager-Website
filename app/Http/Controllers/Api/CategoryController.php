<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function index()
  {
    $categories = Category::all();

    return CategoryResource::collection( $categories);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string'],
      'emoji' => ['nullable', 'string'],
    ]);

    $transactionCategory = Category::create([
      'name' => $request->name,
      'emoji' => $request->emoji
    ]);

    return CategoryResource::make($transactionCategory);
  }

  public function show(Category $transactionCategory)
  {
    return CategoryResource::make($transactionCategory);
  }

  public function update(Request $request, Category $category)
  {
      \Log::info("Updating category with ID: " . $category->id);

      $request->validate([
          'name' => ['required', 'string'],
          'emoji' => ['nullable', 'string'],
      ]);

      $category->update([
          'name'=> $request->name,
          'emoji' => $request->emoji
      ]);

      return CategoryResource::make($category->fresh());
  }
  public function destroy(Category $category)
  {
    $category->delete();

    return response()->noContent();
  }
}
