<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'duration'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class); // InsurancePlan belongs to Client
    }

    public function insurances()
    {
        return $this->hasMany(Insurance::class); // InsurancePlan belongs to Insurance
    }

}
