<?php

namespace App\Models;

use App\Concerns\HasProfilePhoto;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens, HasProfilePhoto, SoftDeletes, Billable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'profile_photo_path',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public static function boot(): void
  {
    parent::boot();

    $incomes = include database_path('data/incomes.php');
    $expenses = include database_path('data/expenses.php');

    static::created(function (User $user) use ($incomes, $expenses) {
      foreach ($incomes as $income) {
        Category::create([
          "name" => $income["name"],
          "emoji" => $income["emoji"],
          "transaction_type" => $income["transaction_type"],
          'user_id' => $user->id
        ]);
      }

      foreach ($expenses as $expense) {
        Category::create([
          "name" => $expense["name"],
          "emoji" => $expense["emoji"],
          "transaction_type" => $expense["transaction_type"],
          'user_id' => $user->id
        ]);
      }
    });
  }
}
