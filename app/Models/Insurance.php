<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'insurance_plan_id',
        'payment_date',
        'expiry_date',
        'user_id',
    ];

    public function insurance_plan()
{
    return $this->belongsTo(InsurancePlan::class, 'insurance_plan_id');
}

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // public function insurancePlan()
    // {
    //     return $this->belongsTo(InsurancePlan::class);
    // }
}
