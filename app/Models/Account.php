<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'type',
    'user_id',
    'rib',
    'balance',
    'account_type',
    'total_expense',
    'total_income',
  ];

  protected $casts = [
    'account_type' => AccountType::class
  ];

  public function transfers()
  {
    return $this->hasMany(Transaction::class, 'source_account_id')
      ->where('transaction_type', TransactionType::TRANSFER);
  }

  public function expenses()
  {
    return $this->hasMany(Transaction::class, 'source_account_id')
      ->where('transaction_type', TransactionType::EXPENSE);
  }

  public function incomes()
  {
    return $this->hasMany(Transaction::class, 'source_account_id')
      ->where('transaction_type', TransactionType::INCOME);
  }
}
