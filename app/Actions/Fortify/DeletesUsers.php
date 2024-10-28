<?php

namespace App\Actions\Fortify;

use App\Models\User;

class DeletesUsers
{
  /**
   * Delete the given user.
   */
  public function delete(User $user): void
  {
    $user->deleteProfilePhoto();
    $user->tokens->each->delete();
    $user->delete();
  }
}
