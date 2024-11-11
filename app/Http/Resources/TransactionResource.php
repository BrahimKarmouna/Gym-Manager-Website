<?php

namespace App\Http\Resources;

use App\Enums\TransactionType;
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
      'id' => $this->id,  // Direct attribute access without `whenHas`
      'date' => $this->date,  // Direct attribute access
      'amount' => $this->amount,  // Direct attribute access
      'source_account' => $this->whenLoaded('sourceAccount', function () {
        return AccountResource::make($this->sourceAccount);  // Eager loaded relationship
      }),
      'destination_account' => $this->whenLoaded('destinationAccount', function () {
        return AccountResource::make($this->destinationAccount);  // Eager loaded relationship
      }),
      'transaction_type' => $this->whenHas('transaction_type', fn() => $this->transaction_type?->toArray()),  // Handle enum properly
      'note' => $this->note,
      'category' => CategoryResource::make($this->whenLoaded("category"))
      ,
      'user' => $this->whenLoaded('user', function () {
        return UserResource::make($this->user);  // Eager loaded relationship
      }),
      'created_at' => $this->whenHas('created_at', fn() => $this->created_at->diffForHumans()),
      'updated_at' => $this->whenHas('updated_at', fn() => $this->updated_at->diffForHumans())
    ];
  }

}
