<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionsResource extends JsonResource
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
        'amount' => $this->content,
        'from' => $this->content,
        'to' => $this->content,
        'note' => $this->content,
        'description' => $this->content,   

  
        'user' => [
          'id' => $this->user->id,
          'name' => $this->user->name,
          'email' => $this->user->email,
          'password' => $this->user->password,
        ]
      ];
    }
}
