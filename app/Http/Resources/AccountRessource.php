<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountRessource extends JsonResource
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
      'rib' => $this->whenHas('rib'),
      'balance' => $this->whenHas('balance'),
      'created_at' => $this->whenHas('created_at'),
    ];
  }
}
