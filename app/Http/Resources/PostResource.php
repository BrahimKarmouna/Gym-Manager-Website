<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'amount' => $this->title,
      'content' => $this->content,
      'created_at' => $this->created_at->diffForHumans(),
      'updated_at' => $this->updated_at->diffForHumans(),

      // 'user' => [
      //   'id' => $this->user->id,
      //   'name' => $this->user->name,
      //   'photo_url' => $this->user->profile_photo_url
      // ]

      'user' => UserResource::make($this->user),
    ];
  }
}
