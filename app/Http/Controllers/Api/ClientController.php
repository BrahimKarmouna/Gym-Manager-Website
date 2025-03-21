<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Gym;

class ClientController extends Controller
{
  // index
  public function getDashboardStats()
  {
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;
    $userId = Auth::id();
    
    $query = Client::query();
    
    // Log authentication status
    Log::info('User ID: ' . $userId . ', Is Admin: ' . (Auth::user()->isAdmin() ? 'Yes' : 'No'));
    
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    Log::info('Authorized Gym IDs: ' . implode(', ', $authorizedGymIds));
    
    // Filter by authorized gyms AND user_id
    $query->whereIn('gym_id', $authorizedGymIds)
          ->where('user_id', $userId); // Explicitly filter by user_id

    // New clients this month
    $newClientsThisMonth = (clone $query)
      ->whereYear('created_at', $currentYear)
      ->whereMonth('created_at', $currentMonth)
      ->count();

    // Active subscriptions - simplified to avoid potential relationship issues
    $activeSubscriptions = (clone $query)
      ->where('subscription_expired_date', '>=', Carbon::now())
      ->count();

    // Expired subscriptions - simplified to avoid potential relationship issues
    $expiredSubscriptions = (clone $query)
      ->where('subscription_expired_date', '<', Carbon::now())
      ->count();

    return response()->json([
      'new_clients_this_month' => $newClientsThisMonth,
      'active_subscriptions' => $activeSubscriptions,
      'expired_subscriptions' => $expiredSubscriptions,
    ]);
  }

  public function index(Request $request)
  {
    // Get the current user ID
    $userId = Auth::id();
    
    // Log the user ID for debugging
    Log::info('Fetching clients for user ID: ' . $userId);
  
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    Log::info('Authorized Gym IDs: ' . implode(', ', $authorizedGymIds));
  
    // Build the query for clients
    $clientsQuery = Client::with(['gym', 'payments', 'payments.plan', 'insurances'])
      ->where('user_id', $userId)
      ->whereIn('gym_id', $authorizedGymIds);
      
    // Apply search filter if provided
    if ($request->has('search') && !empty($request->input('search'))) {
      $search = $request->input('search');
      $clientsQuery->where(function($query) use ($search) {
        $query->where('Full_name', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%')
          ->orWhere('phone', 'like', '%' . $search . '%');
      });
    }
    
    // Apply status filter if provided
    if ($request->has('status') && !empty($request->input('status'))) {
      $clientsQuery->where('status', $request->input('status'));
    }
    
    // Get the clients
    $clients = $clientsQuery->get();
    
    // Add computed properties for the frontend
    $clients->each(function ($client) {
      // Check if client has a valid subscription
      $client->is_payed = $client->getIsPayedAttribute();
      
      // Check if client has valid insurance
      $client->is_assured = $client->getIsAssuredAttribute();
      
      // Log the client's status for debugging
      Log::info("Client {$client->id} ({$client->Full_name}): is_payed = " . 
               ($client->is_payed ? 'true' : 'false') . 
               ", is_assured = " . ($client->is_assured ? 'true' : 'false') .
               ", subscription_expired_date = {$client->subscription_expired_date}" .
               ", assurance_expired_date = {$client->assurance_expired_date}");
    });
      
    // Log the number of clients found
    Log::info('Number of clients found: ' . $clients->count());
    
    // If no clients were found, log all clients for debugging
    if ($clients->count() == 0) {
      $allClients = Client::all();
      Log::info('Total clients in database: ' . $allClients->count());
      
      foreach ($allClients as $client) {
        Log::info("Client ID: {$client->id}, Name: {$client->Full_name}, User ID: {$client->user_id}, Gym ID: {$client->gym_id}");
      }
    }
    
    // Get dashboard stats
    $dashboardStats = $this->getDashboardStats()->getData();
    
    // Return the clients and dashboard stats
    return response()->json([
      'clients' => $clients,
      'new_clients_this_month' => $dashboardStats->new_clients_this_month,
      'active_subscriptions' => $dashboardStats->active_subscriptions,
      'expired_subscriptions' => $dashboardStats->expired_subscriptions,
    ]);
  }

  // post
  public function store(Request $request)
  {
    $request->validate([
      'Full_name' => 'required|string|max:255',
      'date_of_birth' => 'required|date',
      'address' => 'required|string|max:255',
      'id_card_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
      'client_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
      'email' => 'required|email|unique:clients,email',
      'phone' => 'nullable|string|max:20',
      'id_card_number' => 'required',
    ]);

    // Get the user's primary gym (first gym in their list)
    $userGym = Auth::user()->gyms()->first();
    
    if (!$userGym) {
      // This shouldn't happen since we auto-create a gym during registration
      // But just in case, create a new gym for this user
      $gymName = Auth::user()->name . "'s Gym";
      $userGym = Gym::create([
        'name' => $gymName,
        'location' => 'Default Location',
        'user_id' => Auth::id(),  // Set the user_id to the authenticated user's ID
      ]);
      
      // Assign the user to the newly created gym
      Auth::user()->gyms()->attach($userGym->id);
      Log::info("Auto-created gym '{$gymName}' (ID: {$userGym->id}) for existing user " . Auth::id());
    }
    
    $gymId = $userGym->id;
    Log::info('Assigning client to user\'s primary gym: ' . $gymId);

    // Stocker les images dans public/clients
    $idCardPath = $request->file('id_card_picture')->store('clients', 'public');
    $clientPicPath = $request->file('client_picture')->store('clients', 'public');

    $client = Client::create([
      'gym_id' => $gymId,
      'user_id' => Auth::id(), // Associate with authenticated user
      'Full_name' => $request->Full_name,
      'date_of_birth' => $request->date_of_birth,
      'address' => $request->address,
      'email' => $request->email,
      'phone' => $request->phone,
      'id_card_number' => $request->id_card_number,
      'id_card_picture' => $idCardPath,
      'client_picture' => $clientPicPath,
      'status' => 'active',
    ]);

    return response()->json(['message' => 'Client enregistré avec succès!', 'client' => $client]);
  }
  
  //function show
  public function show($id)
  {
    $client = Client::with(['gym', 'payments', 'payments.plan', 'insurances'])
      ->where('id', $id)
      ->where('user_id', Auth::id())
      ->first();
    
    if (!$client) {
      return response()->json(['message' => 'Client not found'], 404);
    }
    
    // Add computed properties using the accessor methods
    $client->is_payed = $client->getIsPayedAttribute();
    $client->is_assured = $client->getIsAssuredAttribute();
    
    // Log client status for debugging
    Log::info("Fetched client {$client->id} ({$client->Full_name}): " .
              "is_payed = " . ($client->is_payed ? 'true' : 'false') . 
              ", is_assured = " . ($client->is_assured ? 'true' : 'false') .
              ", subscription_expired_date = {$client->subscription_expired_date}" .
              ", assurance_expired_date = {$client->assurance_expired_date}");
    
    return response()->json($client);
  }
  
  // Update client
  public function update(Request $request, $id)
  {
    $client = Client::where('id', $id)
      ->where('user_id', Auth::id())
      ->first();
    
    if (!$client) {
      return response()->json(['message' => 'Client not found'], 404);
    }
    
    $request->validate([
      'Full_name' => 'required|string|max:255',
      'date_of_birth' => 'required|date',
      'address' => 'required|string|max:255',
      'email' => 'required|email|unique:clients,email,' . $id,
      'phone' => 'nullable|string|max:20',
      'id_card_number' => 'required',
    ]);
    
    // Handle image updates if provided
    if ($request->hasFile('id_card_picture')) {
      $idCardPath = $request->file('id_card_picture')->store('clients', 'public');
      $client->id_card_picture = $idCardPath;
    }
    
    if ($request->hasFile('client_picture')) {
      $clientPicPath = $request->file('client_picture')->store('clients', 'public');
      $client->client_picture = $clientPicPath;
    }
    
    // Update client data
    $client->Full_name = $request->Full_name;
    $client->date_of_birth = $request->date_of_birth;
    $client->address = $request->address;
    $client->email = $request->email;
    $client->phone = $request->phone;
    $client->id_card_number = $request->id_card_number;
    $client->status = $request->status ?? $client->status;
    
    $client->save();
    
    return response()->json(['message' => 'Client updated successfully', 'client' => $client]);
  }
  
  // Delete client
  public function destroy($id)
  {
    $client = Client::where('id', $id)
      ->where('user_id', Auth::id())
      ->first();
    
    if (!$client) {
      return response()->json(['message' => 'Client not found'], 404);
    }
    
    $client->delete();
    
    return response()->json(['message' => 'Client deleted successfully']);
  }
  
  // Search clients
  public function search(Request $request)
  {
    $search = $request->input('q');
    
    if (empty($search)) {
      return response()->json(['clients' => []]);
    }
    
    $clients = Client::where('user_id', Auth::id())
      ->whereIn('gym_id', Auth::user()->getAuthorizedGymIds())
      ->where(function($query) use ($search) {
        $query->where('Full_name', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%')
          ->orWhere('phone', 'like', '%' . $search . '%');
      })
      ->with(['gym', 'payments'])
      ->get();
    
    return response()->json(['clients' => $clients]);
  }
}
