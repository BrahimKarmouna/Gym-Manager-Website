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

// Test assistant login routes - place these BEFORE the catch-all route
Route::get('/assistant-login-test', [App\Http\Controllers\Auth\AssistantLoginTestController::class, 'showLoginForm'])->name('assistant.login.test')->middleware('web');
Route::post('/assistant-login-test', [App\Http\Controllers\Auth\AssistantLoginTestController::class, 'login'])->name('assistant.login.test.submit')->middleware('web');

// Direct assistant login without form (for testing)
Route::get('/assistant-direct-login', [App\Http\Controllers\Auth\AssistantDirectLoginController::class, 'loginDirect'])->middleware('web');

// Assistant login routes
Route::get('/assistant/login', [App\Http\Controllers\Auth\AssistantLoginController::class, 'showLoginForm'])->name('assistant.login')->middleware('web');
Route::post('/assistant/login', [App\Http\Controllers\Auth\AssistantLoginController::class, 'login'])->name('assistant.login.submit')->middleware('web');
Route::post('/assistant/logout', [App\Http\Controllers\Auth\AssistantLoginController::class, 'logout'])->name('assistant.logout')->middleware('web');

// Added auth.basic fallback for assistant dashboard to prevent Unauthenticated errors
Route::group(['middleware' => ['web', 'auth:assistant']], function () {
    Route::get('/assistant', [App\Http\Controllers\Auth\AssistantDirectLoginController::class, 'dashboard'])->name('assistant.dashboard');
});

// Diagnostic routes for assistant authentication
Route::get('/assistant-auth-diagnostic', [App\Http\Controllers\Auth\AssistantAuthDiagnosticController::class, 'runDiagnostics']);
Route::get('/assistant-auth-test', function() {
    return view('auth.assistant-auth-test');
})->name('assistant.auth.test');

Route::get('{any}', function () {
  return view('index');
})
  ->where('any', '.*')
  ->where('any', '^(?!api).*$');
