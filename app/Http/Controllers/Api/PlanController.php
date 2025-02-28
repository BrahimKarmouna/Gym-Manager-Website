<?php

namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
  public function index()
  {
    //using api
    $plans = Plan::all();
    return response()->json($plans);
  }

  public function show($id)
  {
    $plan = Plan::find($id);
    return response()->json($plan);
  }

  public function store(Request $request)
  {
    $plan = new Plan();
    $plan->name = $request->name;
    $plan->price = $request->price;
    $plan->duration = $request->duration;
    $plan->save();
    return response()->json($plan);
  }
}
