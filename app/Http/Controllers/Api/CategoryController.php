<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

  public function index(Request $request)
  {
    $categories = Category::latest()
      ->where('user_id', auth()->id())
      ->when(
        $request->get('transaction_type'),
        function ($query) use ($request) {
          $query->where('transaction_type', $request->transaction_type);
        }
      )
      ->get();

    return CategoryResource::collection($categories);
  }


  public function store(CategoryRequest $request)
  {
    switch ($request->transaction_type) {
      case TransactionType::INCOME->value:
        $transactionCategory = Category::create([
          'name' => $request->name,
          'emoji' => $request->emoji,
          'transaction_type' => $request->transaction_type,
          'user_id' => auth()->id(),
        ]);
        break;
      case TransactionType::EXPENSE->value:
        $transactionCategory = Category::create([
          'name' => $request->name,
          'emoji' => $request->emoji,
          'transaction_type' => $request->transaction_type,
          'user_id' => auth()->id(),
        ]);
        break;
      default:
        return response()->json(['message' => 'Invalid transaction type'], 400);
    }

    return CategoryResource::make($transactionCategory);
  }

  public function show(Category $transactionCategory)
  {
    return CategoryResource::make($transactionCategory);
  }

  public function update(CategoryRequest $request, Category $category)
  {
    $category->update([
      'name' => $request->name,
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
