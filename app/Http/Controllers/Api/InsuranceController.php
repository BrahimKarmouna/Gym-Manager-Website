<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insurance;  // Ensure this is imported
use App\Models\InsurancePlan;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InsuranceController extends Controller
{
  // Get all insurance records (similar to the PaymentController index method)
  public function index()
  {
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    Log::info('Insurance Controller - Authorized Gym IDs: ' . implode(', ', $authorizedGymIds));
    
    $query = Insurance::query();
    
    // Filter insurance records by clients that belong to authorized gyms AND by user_id
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds)
        ->where('user_id', Auth::id()); // Only show clients created by this user
    });
    
    $Insurance = $query->get();
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
    
    // Fetch client and verify gym authorization
    $client = Client::findOrFail($request->input('client_id'));
    
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    // Check if user has access to the gym this client belongs to
    if (!in_array($client->gym_id, $authorizedGymIds)) {
      return response()->json(['message' => 'You do not have permission to add insurance for this client'], 403);
    }
    
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
      'user_id' => Auth::id(), // Associate with authenticated user
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
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $query = Insurance::query();
    
    // Filter insurance records by clients that belong to authorized gyms AND by user_id
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds)
        ->where('user_id', Auth::id()); // Only show clients created by this user
    });
    
    $insurance = $query->findOrFail($id);
    return response()->json($insurance);
  }
  
  public function update(Request $request, $id)
  {
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $query = Insurance::query();
    
    // Filter insurance records by clients that belong to authorized gyms AND by user_id
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds)
        ->where('user_id', Auth::id()); // Only allow updating insurance for clients created by this user
    });
    
    $insurance = $query->findOrFail($id);

    $request->validate([
      'client_id' => 'exists:clients,id',
      'insurance_plan_id' => 'exists:insurance_plans,id',
      'payment_date' => 'date',
      'expiry_date' => 'date',
    ]);

    // If client_id is being changed, verify authorization for new client
    if ($request->has('client_id') && $request->input('client_id') != $insurance->client_id) {
      $newClient = Client::findOrFail($request->input('client_id'));
      
      // Check if user has access to the gym this client belongs to
      if (!in_array($newClient->gym_id, $authorizedGymIds)) {
        return response()->json(['message' => 'You do not have permission to assign insurance to this client'], 403);
      }
    }

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
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $query = Insurance::query();
    
    // Filter insurance records by clients that belong to authorized gyms
    $query->whereHas('client', function ($q) use ($authorizedGymIds) {
      $q->whereIn('gym_id', $authorizedGymIds);
    });
    
    $insurance = $query->findOrFail($id);
    $insurance->delete();

    return response()->json(['message' => 'Insurance deleted successfully']);
  }
}
