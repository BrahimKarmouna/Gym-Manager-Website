<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  protected $fillable = [
    'user_id',
    'date',
    'amount',
    'source_account_id',
    'destination_account_id',
    'note',
    'category_id',
    'description',
    'transaction_type',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function sourceAccount()
  {
    return $this->belongsTo(Account::class, 'source_account_id');
  }

  public function destinationAccount()
  {
    return $this->belongsTo(Account::class, 'destination_account_id');
  }
  public function category(){
    return $this->belongsTo(Category::class);
  }
}
