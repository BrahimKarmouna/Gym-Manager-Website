<?php

use App\Http\Controllers\Api\CurrentUserController;
use App\Http\Controllers\Api\OtherBrowserSessionsController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\TransactionCategoryController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

// Transaction categories
Route::get('transaction-categories', [TransactionCategoryController::class, 'index']);
Route::get('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'show']);
Route::post('transaction-categories', [TransactionCategoryController::class, 'store']);
Route::put('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'update']);
Route::delete('transaction-categories/{transaction_category}', [TransactionCategoryController::class, 'destroy']);


// Accounts
Route::get('accounts', [TransactionCategoryController::class, 'index']);




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
