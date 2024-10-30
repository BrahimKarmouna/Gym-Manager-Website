<?php

namespace App\Enums;

enum AccountType: string
{
  case CHECKING = 'checking';

  case SAVINGS = 'savings';

  case CREDIT = 'credit';

  case INVESTMENT = 'investment';

  case CASH = 'cash';

  case RETIREMENT = 'retirement';
}
