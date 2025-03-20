<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
  public function index()
  {
    // Only return plans associated with the authenticated user
    $plans = Plan::where('user_id', Auth::id())->get();
    return response()->json($plans);
  }

  public function show($id)
  {
    // Only show plans owned by the authenticated user
    $plan = Plan::where('id', $id)
      ->where('user_id', Auth::id())
      ->first();
      
    if (!$plan) {
      return response()->json(['message' => 'Plan not found or you do not have permission to view this plan'], 404);
    }
    
    return response()->json($plan);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'price' => 'required|numeric',
      'duration' => 'required|integer',
    ]);
    
    $plan = new Plan();
    $plan->name = $request->name;
    $plan->price = $request->price;
    $plan->duration = $request->duration;
    $plan->user_id = Auth::id(); // Associate with authenticated user
    $plan->save();
    
    return response()->json($plan);
  }
}
