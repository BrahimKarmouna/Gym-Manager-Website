<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function gymEnhancements()
    {
        return $this->hasMany(GymEnhancement::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
