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
      'from' =>$this->whenHas('source_account_id'),
      'to' => $this->whenHas('destination_account_id'),
      'note' =>$this->whenHas('note'),
      'category' =>$this->whenHas('category_id'),
      'user' => UserResource::make($this->user),
    ];
  }
}
