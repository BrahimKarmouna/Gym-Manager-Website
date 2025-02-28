<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['gym_id', 'description', 'amount', 'bill_date'];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
