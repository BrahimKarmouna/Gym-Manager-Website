<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;

class OtherBrowserSessionsController extends Controller
{
  /**
   * Log out from other browser sessions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @return \Illuminate\Http\RedirectResponse
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

    $guard->logoutOtherDevices($request->password);

    $this->deleteOtherSessionRecords($request);

    return response()->json([
      'status' => 'other-sessions-destroyed',
    ]);
  }

  /**
   * Delete the other browser session records from storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   */
  protected function deleteOtherSessionRecords(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return;
    }

    DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
      ->where('user_id', $request->user()->getAuthIdentifier())
      ->where('id', '!=', $request->session()->getId())
      ->delete();
  }
}
