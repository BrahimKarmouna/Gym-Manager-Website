<?php

namespace App\Enums;

use Illuminate\Contracts\Support\Arrayable;

enum AccountType: string implements Arrayable
{
  case CHECKING = 'checking';

  case SAVINGS = 'savings';

  case CREDIT = 'credit';

  case INVESTMENT = 'investment';

  case CASH = 'cash';

  case RETIREMENT = 'retirement';


  public function label()
  {
    return match($this) {
      self::CHECKING => 'Checking',
      self::SAVINGS => 'Savings',
      self::CREDIT => 'Credit',
      self::INVESTMENT => 'Investment',
      self::CASH => 'Cash',
      self::RETIREMENT => 'Retirement'
    };
  }

  public function toArray()
  {
    return [
      'label' => $this->label()
    ];
  }
}
