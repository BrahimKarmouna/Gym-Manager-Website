<?php

use App\Http\Controllers\Api\TransactionCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('transaction-categories', [TransactionCategoryController::class, 'index']);
Route::get('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'show']);
Route::delete('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'destroy']);
