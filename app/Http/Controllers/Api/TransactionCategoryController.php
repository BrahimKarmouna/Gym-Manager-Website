<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionCategoryResource;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
  public function index()
  {
    $categories = TransactionCategory::all();

    return TransactionCategoryResource::collection( $categories);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string'],
      'emoji' => ['nullable', 'string'],
    ]);

    $transactionCategory = TransactionCategory::create([
      'name' => $request->name,
      'emoji' => $request->emoji
    ]);

    return TransactionCategoryResource::make($transactionCategory);
  }

  public function show(TransactionCategory $transactionCategory)
  {
    return TransactionCategoryResource::make($transactionCategory);
  }  public function update(Request $request, TransactionCategory $transactionCategory)
  {
      \Log::info("Updating category with ID: " . $transactionCategory->id);

      $request->validate([
          'name' => ['required', 'string'],
          'emoji' => ['nullable', 'string'],
      ]);

      $transactionCategory->update([
          'name'=> $request->name,
          'emoji' => $request->emoji
      ]);

      return TransactionCategoryResource::make($transactionCategory->fresh());
  }
  public function destroy(TransactionCategory $transactionCategory)
  {
    $transactionCategory->delete();

    return response()->noContent();
  }
}
