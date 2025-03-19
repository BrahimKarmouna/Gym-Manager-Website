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


class ClientController extends Controller
{
  // index
  public function getDashboardStats()
  {
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    // New clients this month
    $newClientsThisMonth = Client::whereYear('created_at', $currentYear)
      ->whereMonth('created_at', $currentMonth)
      ->count();

    // Active subscriptions
    $activeSubscriptions = Client::whereHas('payments', function (Builder $query) {
      $query->where('subscription_expired_date', '>=', Carbon::now());
    })->count();

    // Expired subscriptions
    $expiredSubscriptions = Client::whereHas('payments', function (Builder $query) {
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
    $dashboardStats = $this->getDashboardStats();
    
    $clients = QueryBuilder::for(Client::class)
      ->allowedFilters([AllowedFilter::exact('full_name', 'Full_name')])
      ->allowedSorts([
        AllowedSort::field('id', 'id'),
        AllowedSort::field('created_at', 'created_at'),
      ])
      ->with('payments.plan')
      ->when($request->input('search'), function ($query) use ($request) {
        $query->where('Full_name', 'like', '%' . $request->input('search') . '%')
          ->orWhere('email', 'like', '%' . $request->input('search') . '%');
      })
      ->get();
  
    // Get new clients (clients created in the current month)
    $newClients = Client::whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->get();
  
    // Get expired subscriptions
    $expiredSubscriptions = Client::whereHas('payments', function (Builder $query) {
      $query->where('subscription_expired_date', '<', Carbon::now());
    })->get();
  
    return response()->json([
      'dashboard_stats' => $dashboardStats->getData(),
      'clients' => $clients,
      'total_clients' => Client::count(),
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
      'phone' => 'nullable|string|max:20 ',
    ]);

    // Stocker les images dans public/clients
    $idCardPath = $request->file('id_card_picture')->store('clients', 'public');
    $clientPicPath = $request->file('client_picture')->store('clients', 'public');

    $client = Client::create([
      'gym_id' => $request->gym_id,
      'Full_name' => $request->Full_name,
      'date_of_birth' => $request->date_of_birth,
      'address' => $request->address,
      'id_card_picture' => $idCardPath,
      'client_picture' => $clientPicPath,
      'email' => $request->email,
      'phone' => $request->phone,
    ]);

    return response()->json(['message' => 'Client enregistré avec succès!', 'client' => $client]);
  }
  // app/Http/Controllers/ClientController.php
  //function show
  public function show($id)
  {
    $client = Client::find($id);

    if (!$client) {
      return response()->json(['message' => 'Client introuvable'], 404);
    }

    // Formatage du numéro de téléphone
    $phone = $client->phone;
    if ($phone) {
      $phone = preg_replace('/\D/', '', $phone);
      if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
        $phone = '+212 ' . substr($phone, 1, 1) . substr($phone, 2, 3) . '-' . substr($phone, 5, 3) . '-' . substr($phone, 8, 2);
      }
    }
    $client->phone = $phone;

    return response()->json($client);
  }

  // Example in a Controller method
  public function updateClientStatus($clientId)
  {
    $client = Client::findOrFail($clientId);
    $client->updateStatusBasedOnExpiration();

    // Optionally, return some response or data
  }

  public function update(Request $request, $id)
  {
    $client = Client::find($id);

    if (!$client) {
      return response()->json(['message' => 'Client introuvable'], 404);
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
      'gym_id' => 'required|integer',
    ]);

    // Mise à jour des champs texte
    $client->update([
      'Full_name' => $request->Full_name,
      'date_of_birth' => $request->date_of_birth,
      'address' => $request->address,
      'email' => $request->email,
      'phone' => $request->phone,
      'gym_id' => $request->gym_id,
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
