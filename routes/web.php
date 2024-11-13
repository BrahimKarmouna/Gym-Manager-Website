<?php

use Illuminate\Support\Facades\Route;

Route::get('login', function () {
  return view('index');
})->middleware(['guest'])
  ->name('login');

Route::get('reset-password/{token}', function ($token) {
  return view('index');
})->middleware(['guest'])
  ->name('password.reset');

Route::get('{any}', function () {
  return view('index');
})
  ->where('any', '.*')
  ->where('any', '^(?!api).*$');
