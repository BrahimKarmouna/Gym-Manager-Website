<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
  use HasFactory;

  protected $fillable = ['client_id', 'plan_id', 'payment_date', 'user_id'];


  // When a payment is created, update the client's subscription expiration date
  // protected static function boot()
  // {
  //   parent::boot();

  //   static::created(function ($payment) {
  //     $client = $payment->client;
  //     $plan = $payment->plan;

  //     if ($client && $plan) {
  //       // Get the current expiration date (or today if null)
  //       $expiresAt = $client->subscription_expired_date
  //         ? Carbon::parse($client->subscription_expired_date)
  //         : Carbon::today();

  //       // Add the plan's duration (assuming it's stored in months)
  //       $expiresAt->addMonths($plan->duration);

  //       // Update the client's expiration date
  //       $client->update(['subscription_expired_date' => $expiresAt]);
  //     }
  //   });
  // }
  public function client()
  {
    return $this->belongsTo(Client::class);
  }

  protected static function boot()
  {
    parent::boot();

    static::created(function ($payment) {
      $client = $payment->client;
      $plan = $payment->plan;

      if ($client && $plan && $plan->duration) {
        // Get the current expiration date (or today if null)
        $expiresAt = $client->subscription_expired_date
          ? Carbon::parse($client->subscription_expired_date)
          : Carbon::now(); // Use Carbon::now() for better precision

        // Add the plan's duration (assuming it's stored in months)
        $expiresAt->addMonths($plan->duration);

        // Update the client's expiration date
        $client->update(['subscription_expired_date' => $expiresAt->format('Y-m-d')]);
      }
    });
  }



  public function plan()
  {
    return $this->belongsTo(Plan::class);
  }
}
