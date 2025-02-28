<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymEnhancement extends Model
{
    use HasFactory;

    protected $fillable = ['gym_id', 'description', 'cost', 'enhancement_date'];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
