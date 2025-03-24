<?php

namespace App\Models;

use App\Concerns\HasProfilePhoto;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens, HasProfilePhoto, SoftDeletes, Billable, HasRoles;

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
    'is_admin',
    'role',
    'created_by',
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
   * The attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'is_admin' => 'boolean',
      'created_by' => 'integer',
    ];
  }

  /**
   * Check if the user is an admin.
   *
   * @return bool
   */
  public function isAdmin(): bool
  {
    return (bool) $this->is_admin;
  }

  /**
   * Get all clients belonging to this user.
   */
  public function clients()
  {
    return $this->hasMany(Client::class);
  }

  /**
   * Get all gyms this user has access to
   */
  public function gyms()
  {
    return $this->belongsToMany(Gym::class)->select('gyms.id', 'gyms.name', 'gyms.location');
  }

  /**
   * Get all clients from gyms this user has access to
   */
  public function gymClients()
  {
    return Client::whereIn('gym_id', $this->gyms()->pluck('gyms.id'));
  }

  /**
   * Get the list of gym IDs this user has access to
   *
   * @return array
   */
  public function getAuthorizedGymIds()
  {
    // Admin can access all gyms
    if ($this->isAdmin()) {
      return Gym::pluck('id')->toArray();
    }

    // Regular user can only access assigned gyms
    return $this->gyms()->pluck('gyms.id')->toArray();
  }

  /**
   * Get the assistant linked to this user account.
   */
  public function assistant()
  {
    return $this->hasOne(Assistant::class, 'user_account_id');
  }

  /**
   * Get all assistants created/managed by this user (not linked to user account).
   */
  public function managedAssistants()
  {
    return $this->hasMany(Assistant::class, 'user_id');
  }

  /**
   * Check if user has an assistant role.
   *
   * @return bool
   */
  public function isAssistant(): bool
  {
    return $this->assistant()->exists();
  }

  /**
   * Check if user has specific role.
   *
   * @param string $role
   * @return bool
   */
  public function hasUserRole(string $role): bool
  {
    return $this->role === $role;
  }

  /**
   * Get the user who created this user.
   */
  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  /**
   * Get users created by this user.
   */
  public function createdUsers()
  {
    return $this->hasMany(User::class, 'created_by');
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
