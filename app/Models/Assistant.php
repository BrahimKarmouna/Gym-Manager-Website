<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assistant extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'gym_id',
        'user_id',
        'user_account_id',
        'photo',
        'active',
        'last_login',
        'notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'last_login' => 'datetime',
    ];

    /**
     * Get the gym that the assistant belongs to.
     */
    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get the user who created/manages this assistant.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user account linked to this assistant.
     */
    public function userAccount()
    {
        return $this->belongsTo(User::class, 'user_account_id');
    }

    /**
     * Get permissions from the user account if linked.
     * 
     * @return array
     */
    public function getPermissionsViaUser()
    {
        if ($this->userAccount) {
            return $this->userAccount->getAllPermissions()->pluck('name')->toArray();
        }
        
        return [];
    }

    /**
     * Check if this assistant has a specific permission.
     * 
     * @param string $permission
     * @return bool
     */
    public function hasPermissionViaUser(string $permission): bool
    {
        if ($this->userAccount) {
            return $this->userAccount->hasPermissionTo($permission);
        }
        
        return false;
    }

    /**
     * Scope a query to only include active assistants.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Get the teams this assistant belongs to.
     */
    public function teams()
    {
        if ($this->userAccount) {
            return $this->userAccount->teams()->wherePivot('role', 'assistant');
        }
        
        return Team::whereHas('users', function ($query) {
            $query->where('users.id', $this->user_id)
                  ->wherePivot('role', 'assistant');
        });
    }
    
    /**
     * Check if this assistant belongs to a specific team.
     * 
     * @param Team $team
     * @return bool
     */
    public function belongsToTeam(Team $team): bool
    {
        if ($this->userAccount) {
            return $this->userAccount->teams()
                ->wherePivot('role', 'assistant')
                ->where('teams.id', $team->id)
                ->exists();
        }
        
        return false;
    }
    
    /**
     * Create or update a team association for this assistant.
     * 
     * @param Team $team
     * @param array $permissions
     * @return void
     */
    public function associateWithTeam(Team $team, array $permissions = []): void
    {
        if (!$this->userAccount) {
            return;
        }
        
        $this->userAccount->teams()->syncWithoutDetaching([
            $team->id => [
                'role' => 'assistant',
                'permissions' => json_encode($permissions)
            ]
        ]);
    }
}
