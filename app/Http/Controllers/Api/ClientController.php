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

    // Active subscriptions
    $activeSubscriptions = (clone $query)->whereHas('payments', function (Builder $query) {
      $query->where('subscription_expired_date', '>=', Carbon::now());
    })->count();

    // Expired subscriptions
    $expiredSubscriptions = (clone $query)->whereHas('payments', function (Builder $query) {
      $query->where('subscription_expired_date', '<', Carbon::now());
    })->count();

    return response()->json([
      'new_clients_this_month' => $newClientsThisMonth,
      'active_subscriptions' => $activeSubscriptions,
      'expired_subscriptions' => $expiredSubscriptions,
    ]);
  }

  public function index(Request $request)
  {
    // Get dashboard stats for authorized gyms
    $dashboardStats = $this->getDashboardStats();
  
    // Get authorized gym IDs for the current user
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
  
    // Get all clients for the authorized gyms WITH USER ID FILTER
    $clients = Client::with('gym')
      ->where('user_id', Auth::id()) // Only show clients created by the current user
      ->whereIn('gym_id', $authorizedGymIds)
      ->when($request->has('status'), function ($query) use ($request) {
        return $query->where('status', $request->input('status'));
      })
      ->with([
        'payments' => function ($query) {
          $query->orderBy('created_at', 'desc');
        }
      ])
      ->with('payments.plan')
      ->when($request->input('search'), function ($query) use ($request) {
        $query->where('Full_name', 'like', '%' . $request->input('search') . '%')
          ->orWhere('email', 'like', '%' . $request->input('search') . '%');
      })
      ->get();
      
    // Log the number of clients found
    Log::info('Number of clients found: ' . $clients->count());
  
    // Get new clients (clients created in the current month) WITH USER ID FILTER
    $newClientsQuery = Client::whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->where('user_id', Auth::id()) // Only count clients created by the current user
      ->whereIn('gym_id', $authorizedGymIds);
    
    $newClients = $newClientsQuery->get();
  
    // Get expired subscriptions WITH USER ID FILTER
    $expiredSubscriptionsQuery = Client::whereHas('payments', function (Builder $query) {
      $query->where('subscription_expired_date', '<', Carbon::now());
    })
    ->where('user_id', Auth::id()) // Only count clients created by the current user
    ->whereIn('gym_id', Auth::user()->getAuthorizedGymIds());
    
    $expiredSubscriptions = $expiredSubscriptionsQuery->get();
    
    // Get total count for the authorized gyms WITH USER ID FILTER
    $totalClientsCount = Client::where('user_id', Auth::id())
      ->whereIn('gym_id', $authorizedGymIds)
      ->count();
  
    return response()->json([
      'dashboard_stats' => $dashboardStats->getData(),
      'clients' => $clients,
      'total_clients' => $totalClientsCount,
      'new_clients' => $newClients,
      'expired_subscriptions' => $expiredSubscriptions
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
    ]);

    return response()->json(['message' => 'Client enregistré avec succès!', 'client' => $client]);
  }
  
  // app/Http/Controllers/ClientController.php
  //function show
  public function show($id)
  {
    // Find the client by ID and ensure it belongs to the authenticated user
    $client = Client::where('id', $id)
      ->where('user_id', Auth::id()) // Only allow access to clients created by this user
      ->with('gym')
      ->with([
        'payments' => function ($query) {
          $query->orderBy('created_at', 'desc');
        },
        'payments.plan'
      ])
      ->first();

    if (!$client) {
      return response()->json(['message' => 'Client not found or you do not have permission to view this client'], 404);
    }

    return response()->json($client);
  }

  // Example in a Controller method
  public function updateClientStatus($clientId)
  {
    $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
    
    $client = Client::whereIn('gym_id', $authorizedGymIds)
      ->where('id', $clientId)
      ->firstOrFail();
      
    $client->updateStatusBasedOnExpiration();

    // Optionally, return some response or data
  }

  public function update(Request $request, $id)
  {
    // Find the client by ID and ensure it belongs to the authenticated user
    $client = Client::where('id', $id)
      ->where('user_id', Auth::id()) // Only allow updating clients created by this user
      ->first();

    if (!$client) {
      return response()->json(['message' => 'Client not found or you do not have permission to modify this client'], 404);
    }

    // Validation des champs
    $request->validate([
      'Full_name' => 'required|string|max:255',
      'date_of_birth' => 'required|date',
      'address' => 'required|string|max:255',
      'id_card_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
      'client_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
      'email' => 'required|email|unique:clients,email,' . $id,
      'phone' => 'nullable|string|max:20',
      // 'gym_id' removed - clients will stay in their originally assigned gym
    ]);

    // We do not change the client's gym - they will stay in their originally assigned gym

    // Mise à jour des champs texte
    $client->update([
      'Full_name' => $request->Full_name,
      'date_of_birth' => $request->date_of_birth,
      'address' => $request->address,
      'email' => $request->email,
      'phone' => $request->phone,
      // user_id and gym_id remain unchanged
    ]);

    // Mise à jour de l'image de la carte d'identité si une nouvelle image est envoyée
    if ($request->hasFile('id_card_picture')) {
      $idCardPath = $request->file('id_card_picture')->store('clients', 'public');
      $client->id_card_picture = $idCardPath;
    }

    // Mise à jour de l'image du client si une nouvelle image est envoyée
    if ($request->hasFile('client_picture')) {
      $clientPicPath = $request->file('client_picture')->store('clients', 'public');
      $client->client_picture = $clientPicPath;
    }

    $client->save();

    return response()->json(['message' => 'Client mis à jour avec succès!', 'client' => $client]);
  }
}
