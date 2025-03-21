<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GymController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();
        
        // Return all gyms the user has access to
        if ($user->isAdmin()) {
            $gyms = Gym::all();
        } else {
            $gyms = $user->gyms;
        }
        
        return response()->json($gyms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $user = $request->user();

        // Create the gym
        $gym = Gym::create([
            'name' => $request->name,
            'location' => $request->location,
            'user_id' => $user->id,
        ]);

        // Associate the user with the gym
        $user->gyms()->attach($gym->id);

        return response()->json($gym, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gym = Gym::findOrFail($id);
        return response()->json($gym);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the gym
        $gym = Gym::findOrFail($id);

        // Update the gym
        $gym->update($request->only(['name', 'location']));

        return response()->json($gym);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gym = Gym::findOrFail($id);
        $gym->delete();
        
        return response()->json(['message' => 'Gym deleted successfully']);
    }
}
