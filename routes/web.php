<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('login', function () {
  return view('index');
})->middleware(['guest'])
  ->name('login');

Route::get('reset-password/{token}', function ($token) {
  return view('index');
})->middleware(['guest'])
  ->name('password.reset');

Route::get('/', function () {
  return view('index');
})->name('home');

Route::get('{any}', function () {
  return view('index');
})
  ->where('any', '.*')
  ->where('any', '^(?!api).*$');
