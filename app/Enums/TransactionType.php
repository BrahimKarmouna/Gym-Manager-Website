<?php

namespace App\Enums;

use Illuminate\Contracts\Support\Arrayable;

enum TransactionType: string implements Arrayable
{
  case TRANSFER = "transfer";
  case INCOME = "income";
  case EXPENSE = 'expense';

  public function label(): string
  {
    return match ($this) {
      TransactionType::TRANSFER => 'Transfer',
      TransactionType::INCOME => 'Income',
      TransactionType::EXPENSE => 'Expense'
    };
  }


  public function textColor(): string
  {
    return match ($this) {
      TransactionType::TRANSFER => 'blue-500', // Return yellow for text color
      TransactionType::INCOME => 'green-500',
      TransactionType::EXPENSE => 'red-500',
    };
  }

  public function bgColor(): string
  {
    return match ($this) {
      TransactionType::TRANSFER => 'blue-100', // Return yellow for background color
      TransactionType::INCOME => 'green-100',
      TransactionType::EXPENSE => 'red-100',
    };
  }


  public function icon(): string
  {
    return match ($this) {
      TransactionType::TRANSFER => 'swap_horiz',
      TransactionType::INCOME => 'trending_up',
      TransactionType::EXPENSE => 'trending_down'
    };
  }

  public function toArray()
  {
    return [
      'value' => $this->value,
      'label' => $this->label(),
      'bgColor' => $this->bgColor(),
      'textColor' => $this->textColor(),
      'icon' => $this->icon()
    ];
  }
}
