<?php

namespace App\Models;

use App\Enums\AccountType;
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
}
