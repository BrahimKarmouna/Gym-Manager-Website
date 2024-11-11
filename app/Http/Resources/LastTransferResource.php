<?php
// app/Http/Resources/LastTransferResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LastTransferResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'date' => $this->date,
      'amount' => $this->amount,
      'source_account' => $this->sourceAccount->name, // Assuming you want to include the account name
      'destination_account' => $this->destinationAccount->name,
      'note' => $this->note,
      'transaction_type' => $this->transaction_type,
    ];
  }
}
