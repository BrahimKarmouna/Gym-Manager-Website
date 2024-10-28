<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\DeletesUsers;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;

class CurrentUserController extends Controller
{
  /**
   * Delete the current user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, StatefulGuard $guard)
  {
    $confirmed = app(ConfirmPassword::class)(
      $guard,
      $request->user(),
      $request->password
    );

    if (!$confirmed) {
      throw ValidationException::withMessages([
        'password' => __('The password is incorrect.'),
      ]);
    }

    app(DeletesUsers::class)->delete($request->user()->fresh());

    $guard->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->noContent();
  }
}
