<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['gym_id', 'name', 'price', 'stock'];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
