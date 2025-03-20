<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsurancePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsurancePlansController extends Controller
{
  /**
   * Get all insurance plans.
   */
  public function index()
  {
    // Only return insurance plans associated with the authenticated user
    $plans = InsurancePlan::where('user_id', Auth::id())->get();
    return response()->json($plans);
  }

  /**
   * Store a new insurance plan.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|unique:insurance_plans,name',
      'price' => 'required|numeric|min:0',
      'duration' => 'required|integer|min:1', // Duration in months
    ]);

    // Merge user_id with the request data
    $data = array_merge($request->all(), ['user_id' => Auth::id()]);
    
    $plan = InsurancePlan::create($data);

    return response()->json([
      'message' => 'Insurance plan created successfully',
      'plan' => $plan
    ]);
  }

  /**
   * Get a specific insurance plan.
   */
  public function show($id)
  {
    // Only show insurance plans owned by the authenticated user
    $plan = InsurancePlan::where('id', $id)
      ->where('user_id', Auth::id())
      ->firstOrFail();
      
    return response()->json($plan);
  }

  /**
   * Update an insurance plan.
   */
  public function update(Request $request, $id)
  {
    // Only allow updating insurance plans owned by the authenticated user
    $plan = InsurancePlan::where('id', $id)
      ->where('user_id', Auth::id())
      ->firstOrFail();

    $request->validate([
      'name' => 'required|string|unique:insurance_plans,name,' . $id,
      'price' => 'required|numeric|min:0',
      'duration' => 'required|integer|min:1',
    ]);

    $plan->update($request->all());

    return response()->json([
      'message' => 'Insurance plan updated successfully',
      'plan' => $plan
    ]);
  }

  /**
   * Delete an insurance plan.
   */
  public function destroy($id)
  {
    // Only allow deleting insurance plans owned by the authenticated user
    $plan = InsurancePlan::where('id', $id)
      ->where('user_id', Auth::id())
      ->firstOrFail();

    // Check if the insurance plan is associated with any insurance record
    if ($plan->insurances()->exists()) {
      return response()->json([
        'message' => 'Cannot delete insurance plan. It is associated with insurance records.'
      ], 400);
    }

    // Delete the insurance plan
    $plan->delete();

    return response()->json(['message' => 'Insurance plan deleted successfully']);
  }

}
