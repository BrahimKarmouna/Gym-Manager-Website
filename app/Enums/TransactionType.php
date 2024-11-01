<?php

namespace App\Enums;

enum TransactionType: string
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


    public function color(): string
    {
      return match ($this) {
        TransactionType::TRANSFER => 'green',
        TransactionType::INCOME => 'blue',
        TransactionType::EXPENSE => 'blue'
      };
    }

    public function toArray()
    {
      return [
        'value' => $this->value,
        'label' => $this->label(),
        'color' => $this->color()
      ];
    }
  }



