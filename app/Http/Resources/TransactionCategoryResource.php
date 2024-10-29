<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionCategoryResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      "id" => $this->id,
      "name" => $this->name,
      "slug" => \Str::slug($this->id . '-' . $this->name),
      "emoji" => $this->emoji,
      "created_at" => $this->created_at->diffForHumans(),
      "updated_at" => $this->updated_at->diffForHumans(),
    ];
  }
}
