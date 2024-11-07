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
      'id' => $this->whenHas('id'),
      'date' => $this->whenHas('date'),
      'amount' => $this->whenHas('amount'),
      'sourceAccount' => AccountResource::make($this->whenLoaded('sourceAccount')),
      'destinationAccount' => AccountResource::make($this->whenLoaded('destinationAccount')),
      'transaction_type' => $this->whenHas('transaction_type'),
      'note' => $this->whenHas('note'),
      'category' => CategoryResource::make($this->category),
      'user' => UserResource::make($this->user),
      // 'created_at' => $this->when($this->created_at, $this->created_at->diffForHumans()),
      // 'updated_at' => $this->when($this->updated_at, $this->updated_at->diffForHumans()),
    ];
  }
}
