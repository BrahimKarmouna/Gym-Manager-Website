<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'duration'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
