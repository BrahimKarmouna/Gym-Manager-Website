<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymUser extends Model
{
    use HasFactory;

    // Specify the table name explicitly to match the migration
    protected $table = 'gym_user';

    protected $fillable = [
        'user_id',
        'gym_id',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
