<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CurrentUserController;
use App\Http\Controllers\Api\OtherBrowserSessionsController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

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


  //! Categories
Route::apiResource('categories', CategoryController::class);

//! Accounts
Route::apiResource('accounts', AccountController::class);

//! POSTS
Route::apiResource('posts', PostController::class);

//! Transactions
Route::apiResource('transactions', TransactionController::class);
