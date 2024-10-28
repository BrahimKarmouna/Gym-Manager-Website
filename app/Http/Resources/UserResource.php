<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->whenHas('id'),
      'name' => $this->whenHas('name'),
      'email' => $this->whenHas('email'),
      'created_at' => $this->whenHas('created_at'),
      'profile_photo_url' => $this->whenAppended('profile_photo_url'),
      'updated_at' => $this->whenHas('updated_at'),
      'email_verified_at' => $this->whenHas('email_verified_at', fn($value) => $value?->diffForHumans()),
      'two_factor_enabled' => $this->hasEnabledTwoFactorAuthentication(),
    ];
  }
}
