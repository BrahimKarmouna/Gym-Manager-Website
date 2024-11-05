<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
        'rib'=> $this->whenHas('rib'),
        'balance' => $this->whenHas('balance'),
        'expenses' => TransactionResource::collection($this->expenses),
        'incomes' => TransactionResource::collection($this->incomes),
        'transfers' => TransactionResource::collection($this->transfers),
        'created_at' => $this->when($this->created_at, $this->created_at->diffForHumans()),
        'updated_at' => $this->when($this->updated_at, $this->updated_at->diffForHumans()),
      ];
    }
}
