<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
  public function index()
  {
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    Log::info('Payment Controller - Authorized Gym IDs: ' . implode(', ', $authorizedGymIds));
    
    $query = Payment::with(['client', 'plan']);
    
    // Filter payment records by clients that belong to authorized gyms AND by user_id
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds)
        ->where('user_id', Auth::id()); // Only show clients created by this user
    });
    
    $payments = $query->get();
    return response()->json($payments);
  }

  public function store(Request $request)
  {
    $request->validate([
      'client_id' => 'required|exists:clients,id',
      'plan_id' => 'required|exists:plans,id',
      'payment_date' => 'required|date',
    ]);

    $plan = Plan::findOrFail($request->input('plan_id'));
    $client = Client::findOrFail($request->input('client_id'));
    
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    // Check if user has access to the gym this client belongs to
    if (!in_array($client->gym_id, $authorizedGymIds)) {
      return response()->json(['message' => 'You do not have permission to add payment for this client'], 403);
    }

    // Get latest expiration date
    $currentExpiration = $client->subscription_expired_date ? Carbon::parse($client->subscription_expired_date) : null;

    // Calculate new expiration date: If expired, start from today; otherwise, extend from last expiration
    if ($currentExpiration && $currentExpiration->isFuture()) {
      $newExpirationDate = $currentExpiration->addMonths($plan->duration);
    } else {
      $newExpirationDate = Carbon::parse($request->payment_date)->addMonths($plan->duration);
    }

    // Save payment
    Payment::create([
      'client_id' => $client->id,
      'plan_id' => $plan->id,
      'payment_date' => Carbon::parse($request->payment_date)->format('Y-m-d'),
      'user_id' => Auth::id(), // Associate with authenticated user
    ]);

    // Update the client's subscription expiration date
    $client->update(['subscription_expired_date' => $newExpirationDate]);

    return response()->json(['message' => 'Payment saved and subscription updated.', 'new_expiration_date' => $newExpirationDate]);
  }


  public function show($id)
  {
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $query = Payment::with(['client', 'plan']);
    
    // Filter payment records by clients that belong to authorized gyms AND by user_id
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds)
        ->where('user_id', Auth::id()); // Only show clients created by this user
    });
    
    $payment = $query->findOrFail($id);
    return response()->json($payment);
  }

  public function update(Request $request, $id)
  {
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $query = Payment::query();
    
    // Filter payment records by clients that belong to authorized gyms AND by user_id
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds)
        ->where('user_id', Auth::id()); // Only allow updating payments for clients created by this user
    });
    
    $payment = $query->findOrFail($id);
    
    // If client_id is provided, verify user has permission for the new client
    if ($request->has('client_id') && $request->input('client_id') != $payment->client_id) {
      $newClient = Client::findOrFail($request->input('client_id'));
      
      // Check if user has access to the gym this client belongs to
      if (!in_array($newClient->gym_id, $authorizedGymIds)) {
        return response()->json(['message' => 'You do not have permission to assign payment to this client'], 403);
      }
    }
    
    $payment->update($request->all());
    return response()->json($payment);
  }

  public function destroy($id)
  {
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $query = Payment::query();
    
    // Filter payment records by clients that belong to authorized gyms
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds);
    });
    
    $payment = $query->findOrFail($id);
    $clientId = $payment->client_id;

    // Delete the payment
    $payment->delete();

    // Get the last remaining payment for the client
    $lastPayment = Payment::where('client_id', $clientId)->orderBy('payment_date', 'desc')->first();

    if ($lastPayment) {
      // Recalculate expiration date based on the last payment
      $newExpirationDate = Carbon::parse($lastPayment->payment_date)->addMonths($lastPayment->plan->duration);
    } else {
      // If no payments remain, set expiration to now
      $newExpirationDate = Carbon::now();
    }

    // Update the client's expiration date
    Client::where('id', $clientId)->update(['subscription_expired_date' => $newExpirationDate]);

    return response()->json(['message' => 'Payment deleted and subscription updated.']);
  }
}
