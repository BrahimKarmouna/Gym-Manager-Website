<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionCategoryResource;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $categories = TransactionCategory::all();

    return TransactionCategoryResource::collection($categories);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(TransactionCategory $transactionCategory)
  {
    return TransactionCategoryResource::make($transactionCategory);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, TransactionCategory $transactionCategory)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(TransactionCategory $transactionCategory)
  {
    $transactionCategory->delete();

    // TransactionCategory::where('id', $transactionCategory->id)->delete();

    // $item = TransactionCategory::where('id', $transactionCategory->id)->first();

    // $item->delete();

    return response()->noContent();
  }
}
