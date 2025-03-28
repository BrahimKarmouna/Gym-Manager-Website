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
      'profile_photo_url' => $this->whenAppended('profile_photo_url'),
      'permissions' => $this->whenHas('permissions'),
    ];
  }
}
