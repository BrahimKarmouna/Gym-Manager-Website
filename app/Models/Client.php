<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'Full_name',
        'date_of_birth',
        'address',
        'id_card_picture',
        'client_picture',
        'id_card_number',
        'email',
        'phone',
        'subscription_expired_date',
        'assurance_expired_date',
        'is_payed', // ✅ Added so it can be mass assigned
    ];

    /**
     * Relationship: A client belongs to a gym.
     */
    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Relationship: A client can have multiple payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    // In the Client Model
    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }


    /**
     * Relationship: Insurance payments (if applicable)
     */


    /**
     * Dynamic attribute for payment status
     * ✅ No need to store `is_payed` in the database.
     */
    public function getIsPayedAttribute()
    {
        return $this->subscription_expired_date && Carbon::parse($this->subscription_expired_date)->isFuture();
    }

    /**
     * Update status based on expiration dates.
     */
    public function updateStatusBasedOnExpiration()
    {
        $this->is_payed = $this->getIsPayedAttribute(); // Use accessor for consistency
        $this->save();
    }

    /**
     * Mutator: Format `subscription_expired_date` before saving to DB.
     */
    public function setSubscriptionExpiredDateAttribute($value)
    {
        $this->attributes['subscription_expired_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    /**
     * Mutator: Format `assurance_expired_date` before saving to DB.
     */
    public function setAssuranceExpiredDateAttribute($value)
    {
        $this->attributes['assurance_expired_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}
