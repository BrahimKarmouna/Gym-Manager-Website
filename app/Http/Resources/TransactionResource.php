<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
      'amount' => $this->amount,
      'from' => $this->sourceAccount,
      'to' => $this->destinationAccount,
      'note' => $this->note,
      // 'description' => $this->content,
      'category' => $this->category,
      // 'user' => [
      //   'id' => $this->user->id,
      //   'name' => $this->user->name,
      //   'photo_url' => $this->user->profile_photo_url,
      //   'email' => $this->user->email,
      // ]

      'user' => UserResource::make($this->user),
    ];
  }
}