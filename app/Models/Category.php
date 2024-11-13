<?php
namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'emoji',
    'transaction_type',
    'user_id',
  ];

  protected $casts = [
    'transaction_type' => TransactionType::class,
  ];

  public function transactions()
  {
    return $this->hasMany(Transaction::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
