<?php
namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'emoji',
    'transaction_type'
  ];

  protected $casts = [
    'transaction_type' => TransactionType::class,
  ];

  public function transactions()
  {
    return $this->hasMany(Transaction::class);
  }
}
