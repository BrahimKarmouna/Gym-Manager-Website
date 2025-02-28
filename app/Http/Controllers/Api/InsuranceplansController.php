<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsurancePlan;
use Illuminate\Http\Request;

class InsurancePlansController extends Controller
{
  /**
   * Get all insurance plans.
   */
  public function index()
  {
    $plans = InsurancePlan::all();
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

    $plan = InsurancePlan::create($request->all());

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
    $plan = InsurancePlan::findOrFail($id);
    return response()->json($plan);
  }

  /**
   * Update an insurance plan.
   */
  public function update(Request $request, $id)
  {
    $plan = InsurancePlan::findOrFail($id);

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
    // Find the insurance plan
    $plan = InsurancePlan::findOrFail($id);

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
