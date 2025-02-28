<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
  public function index()
  {
    $payments = Payment::with(['client', 'plan'])->get();
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
    ]);

    // ✅ Update the client's subscription expiration date
    $client->update(['subscription_expired_date' => $newExpirationDate]);

    return response()->json(['message' => 'Payment saved and subscription updated.', 'new_expiration_date' => $newExpirationDate]);
  }


  public function show($id)
  {
    $payment = Payment::with(['client', 'plan'])->findOrFail($id);
    return response()->json($payment);
  }

  public function update(Request $request, $id)
  {
    $payment = Payment::findOrFail($id);
    $payment->update($request->all());
    return response()->json($payment);
  }

  public function destroy($id)
  {
    $payment = Payment::findOrFail($id);
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
