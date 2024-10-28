<?php

use Illuminate\Support\Facades\Route;

// Route::get('login', function () {
//   return \File::get(public_path('index.html'));
// })->middleware(['guest'])
//   ->name('login');

Route::get('reset-password/{token}', function ($token) {
  return \File::get(public_path('index.html'));
})->middleware(['guest'])
  ->name('password.reset');

Route::get('{any}', function () {
  return view('index');
})
->where('any', '.*')
->where('any', '^(?!api).*$');
