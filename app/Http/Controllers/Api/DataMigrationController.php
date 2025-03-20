<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DataMigrationController extends Controller
{
    /**
     * Assign clients to the currently authenticated user
     */
    public function assignClientsToCurrentUser(Request $request)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        
        // Log current user information
        Log::info('Current user: ID=' . $currentUser->id . ', Name=' . $currentUser->name . ', Is Admin=' . ($currentUser->is_admin ? 'Yes' : 'No'));
        
        // Update admin status if requested (only for development purposes)
        if ($request->has('set_admin') && in_array($request->get('set_admin'), ['0', '1'])) {
            $isAdmin = (bool)$request->get('set_admin');
            $currentUser->is_admin = $isAdmin;
            $currentUser->save();
            
            Log::info('Updated admin status for user ' . $currentUser->id . ' to ' . ($isAdmin ? 'Admin' : 'Not Admin'));
        }
        
        // Count unassigned clients
        $unassignedCount = Client::whereNull('user_id')->count();
        
        // Count total clients
        $totalCount = Client::count();
        
        // Assign all unassigned clients to current user
        $updated = DB::table('clients')
            ->whereNull('user_id')
            ->update(['user_id' => $currentUser->id]);
            
        Log::info('Assigned ' . $updated . ' clients to user ' . $currentUser->id);
        
        // Option to claim all clients
        if ($request->has('claim_all') && $request->get('claim_all') == '1') {
            $updatedAll = DB::table('clients')
                ->update(['user_id' => $currentUser->id]);
                
            Log::info('Claimed all ' . $updatedAll . ' clients for user ' . $currentUser->id);
            
            return response()->json([
                'message' => 'All clients have been assigned to you',
                'updated_count' => $updatedAll,
                'your_user_id' => $currentUser->id,
                'is_admin' => $currentUser->is_admin
            ]);
        }
        
        return response()->json([
            'message' => 'Unassigned clients have been assigned to you',
            'updated_count' => $updated,
            'unassigned_count' => $unassignedCount,
            'total_clients' => $totalCount,
            'your_user_id' => $currentUser->id,
            'is_admin' => $currentUser->is_admin
        ]);
    }
    
    /**
     * List all users with their admin status
     */
    public function listUsers()
    {
        $users = User::select('id', 'name', 'email', 'is_admin')->get();
        
        return response()->json([
            'users' => $users,
            'current_user_id' => Auth::id()
        ]);
    }
}
