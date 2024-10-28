<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfilePhotoController extends Controller
{
  /**
   * Delete the current user's profile photo.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Request $request)
  {
    $request->user()->deleteProfilePhoto();

    return response()->json([
      'status' => 'profile-photo-deleted',

      'user' => UserResource::make($request->user()),
    ]);
  }
}
