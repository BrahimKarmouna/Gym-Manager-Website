<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Agent;
use App\Http\Controllers\Api\Concerns\ConfirmsTwoFactorAuthentication;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Features;

class UserProfileController extends Controller
{
  use ConfirmsTwoFactorAuthentication;

  public function me(Request $request)
  {
    $user = auth()->user();

    $user->append('profile_photo_url');
    $user->permissions = $user->getAllPermissions()->pluck('name');

    return UserResource::make($request->user());
  }

  public function show(Request $request)
  {
    $this->validateTwoFactorAuthenticationState($request);

    return response()->json([
      'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
      'sessions' => $this->sessions($request)->all(),
    ]);
  }

  /**
   * Get the current sessions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Support\Collection
   */
  public function sessions(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return collect();
    }

    return collect(
      \DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
        ->where('user_id', $request->user()->getAuthIdentifier())
        ->orderBy('last_activity', 'desc')
        ->get()
    )->map(function ($session) use ($request) {
      $agent = $this->createAgent($session);

      return (object) [
        'agent' => [
          'is_desktop' => $agent->isDesktop(),
          'platform' => $agent->platform(),
          'browser' => $agent->browser(),
        ],
        'ip_address' => $session->ip_address,
        'is_current_device' => $session->id === $request->session()->getId(),
        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
      ];
    });
  }

  /**
   * Create a new agent instance from the given session.
   *
   * @param  mixed  $session
   * @return Agent
   */
  protected function createAgent($session)
  {
    return tap(new Agent(), fn(Agent $agent) => $agent->setUserAgent($session->user_agent));
  }
}
