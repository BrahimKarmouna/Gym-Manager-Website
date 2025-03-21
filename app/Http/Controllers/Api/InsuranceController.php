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
    Log::info('Insurance store request received: ' . json_encode($request->all()));
    
    // Validation to ensure required fields are present
    $request->validate([
      'client_id' => 'required|exists:clients,id',
      'insurance_plan_id' => 'required|exists:insurance_plans,id',
      'payment_date' => 'required|date',
      'expiry_date' => 'required|date',
    ]);
    
    // Fetch client and verify gym authorization
    $client = Client::findOrFail($request->input('client_id'));
    Log::info('Client found: ' . $client->Full_name . ' (ID: ' . $client->id . ')');
    
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    // Check if user has access to the gym this client belongs to
    if (!in_array($client->gym_id, $authorizedGymIds)) {
      Log::warning('User does not have permission to add insurance for client ' . $client->id);
      return response()->json(['message' => 'You do not have permission to add insurance for this client'], 403);
    }
    
    $plan = InsurancePlan::findOrFail($request->input('insurance_plan_id'));
    Log::info('Plan found: ' . $plan->name . ' (ID: ' . $plan->id . ')');

    // Parse the dates
    $paymentDate = Carbon::parse($request->input('payment_date'));
    $expiryDate = Carbon::parse($request->input('expiry_date'));
    
    // If expiry date is before payment date, adjust it
    if ($expiryDate->lessThan($paymentDate)) {
      $expiryDate = $paymentDate->copy()->addMonths($plan->duration);
    }
    
    // Create the insurance record in the database
    $insurance = Insurance::create([
      'client_id' => $client->id,
      'insurance_plan_id' => $plan->id,
      'payment_date' => $paymentDate->format('Y-m-d'),
      'expiry_date' => $expiryDate->format('Y-m-d'),
      'user_id' => Auth::id(), // Associate with authenticated user
    ]);
    
    Log::info('Insurance created: ID ' . $insurance->id);

    // Update the client's insurance expiration date
    $client->assurance_expired_date = $expiryDate->format('Y-m-d');
    $client->save();
    
    Log::info('Client assurance_expired_date updated to ' . $expiryDate->format('Y-m-d'));
    
    // Force refresh the client from the database to ensure we have the latest data
    $client->refresh();

    // Return a success response with the new insurance record
    return response()->json([
      'message' => 'Insurance saved successfully.',
      'insurance' => $insurance,
      'client' => $client,
      'plan' => $plan
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
    if ($client && isset($client->assurance_expired_date)) {
      $client->update(['assurance_expired_date' => $insurance->expiry_date]);
      Log::info('Client assurance_expired_date updated to ' . $insurance->expiry_date);
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
