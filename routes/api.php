<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CurrentUserController;
use App\Http\Controllers\Api\OtherBrowserSessionsController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\TransactionCategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

// Transaction categories
// Route::get('transaction-categories', [TransactionCategoryController::class, 'index']);
// Route::get('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'show']);
// Route::post('transaction-categories', [TransactionCategoryController::class, 'store']);
// Route::put('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'update']);
// Route::delete('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'destroy']);

Route::apiResource('transaction_categories', TransactionCategoryController::class);

Route::apiResource('accounts', AccountController::class);

// Accounts






// Route::get('posts', [PostController::class, 'index']);
// Route::delete('posts/{post}', [PostController::class, 'destroy']);
// Route::post('posts', [PostController::class, 'store']);
// Route::put('posts/{post}', [PostController::class, 'update']);
// Route::get('posts/{post}', [PostController::class, 'show']);

Route::apiResource('posts', PostController::class);

Route::apiResource('transactions', TransactionController::class);
Route::get('transactions/income', [TransactionController::class, '']);

Route::get('transactions/transfer', [TransactionController::class, 'indextransfer']);
Route::get('transactions/income', [TransactionController::class, 'indexincome']);
Route::get('transactions/expense', [TransactionController::class, 'indexexpense']);

// Transactions



// Auth
Route::name('api.')
  ->middleware(['auth:sanctum'])
  ->group(function () {
    Route::get('me', [UserProfileController::class, 'me'])->name('auth.user');

    Route::get('/user/profile', [UserProfileController::class, 'show'])->name('profile.show');

    // Delete other browser sessions
    Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
      ->name('other-browser-sessions.destroy');

    // Delete profile photo
    Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
      ->name('current-user-photo.destroy');

    // Delete profile
    Route::delete('/user', [CurrentUserController::class, 'destroy'])
      ->name('current-user.destroy');

  });
