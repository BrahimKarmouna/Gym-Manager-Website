<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insurance;  // Ensure this is imported
use App\Models\InsurancePlan;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
  // Get all insurance records (similar to the PaymentController index method)
  public function index()
  {
    $Insurance = Insurance::all();
    return response()->json($Insurance);
  }

  // Store a new insurance record (similar to store method in PaymentController)
  public function store(Request $request)
  {
    // Validation to ensure required fields are present
    $request->validate([
      'client_id' => 'required|exists:clients,id',
      'insurance_plan_id' => 'required|exists:insurance_plans,id',
      'payment_date' => 'required|date',
      'expiry_date' => 'required|date',
    ]);

    // Fetch client and plan details
    $client = Client::findOrFail($request->input('client_id'));
    $plan = InsurancePlan::findOrFail($request->input('insurance_plan_id'));

    // Fetch the latest insurance record for the client
    $insurance = Insurance::where('client_id', $client->id)->orderBy('expiry_date', 'desc')->first();
    $insuranceExpiration = $insurance ? Carbon::parse($insurance->expiry_date) : null;

    // Calculate the new expiration date
    if ($insuranceExpiration && $insuranceExpiration->isFuture()) {
      // If the last insurance expiration date is in the future, extend it
      $newInsuranceExpiration = $insuranceExpiration->addMonths($plan->duration);
    } else {
      // Otherwise, use the provided expiry_date and add the plan duration
      $newInsuranceExpiration = Carbon::parse($request->expiry_date)->addMonths($plan->duration);
    }

    // Create the insurance record in the database
    $insurance = Insurance::create([
      'client_id' => $client->id,
      'insurance_plan_id' => $plan->id,
      'payment_date' => Carbon::parse($request->payment_date)->format('Y-m-d'),
      'expiry_date' => $newInsuranceExpiration->format('Y-m-d'),
    ]);

    // Update the client's insurance expiration date
    $client->update(['assurance_expired_date' => $newInsuranceExpiration]);

    // Return a success response with the new expiration date
    return response()->json([
      'message' => 'Insurance saved and expiration date updated.',
      'new_expiration_date' => $newInsuranceExpiration
    ]);
  }

  public function show($id)
  {
    $insurance = Insurance::findOrFail($id);
    return response()->json($insurance);
  }
  public function update(Request $request, $id)
  {
    $insurance = Insurance::findOrFail($id);

    $request->validate([
      'client_id' => 'exists:clients,id',
      'insurance_plan_id' => 'exists:insurance_plans,id',
      'payment_date' => 'date',
      'expiry_date' => 'date',
    ]);

    $client = $insurance->client;
    $plan = $insurance->plan;

    // Update the insurance record
    $insurance->update([
      'client_id' => $request->input('client_id', $insurance->client_id),
      'insurance_plan_id' => $request->input('insurance_plan_id', $insurance->insurance_plan_id),
      'payment_date' => $request->input('payment_date', $insurance->payment_date),
      'expiry_date' => $request->input('expiry_date', $insurance->expiry_date),
    ]);

    // Update the client's insurance expiration date if necessary
    if ($client && $plan) {
      $insuranceExpiration = Carbon::parse($insurance->expiry_date);
      $newInsuranceExpiration = $insuranceExpiration->addMonths($plan->duration);
      $client->update(['assurance_expired_date' => $newInsuranceExpiration]);
    }

    return response()->json([
      'message' => 'Insurance updated successfully.',
      'insurance' => $insurance
    ]);
  }

  public function destroy($id)
  {
    $insurance = Insurance::findOrFail($id);
    $insurance->delete();

    return response()->json(['message' => 'Insurance deleted successfully']);
  }

  // public function destroy($id)
  // {
  //     $insurance = Insurance::findOrFail($id);
  //     $client = $insurance->client;
  //     $plan = $insurance->plan;

  //     // Delete the insurance record
  //     $insurance->delete();

  //     // Update the client's insurance expiration date if necessary
  //     if ($client && $plan) {
  //         $latestInsurance = $client->insurances->sortByDesc('expiry_date')->first();
  //         $newInsuranceExpiration = $latestInsurance ? Carbon::parse($latestInsurance->expiry_date) : null;
  //         $client->update(['assurance_expired_date' => $newInsuranceExpiration]);
  //     }

  //     return response()->json([
  //         'message' => 'Insurance deleted successfully.'
  //     ]);
  // }
}
