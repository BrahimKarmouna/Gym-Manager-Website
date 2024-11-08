<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class HomeController extends Controller
{


  public function index(Request $request)
  {
    $transfers = Transaction::where("user_id", auth()->user()->id)->where('transaction_type', 'transfer')->count();
    $incomes = Transaction::where('user_id', auth()->user()->id)->where('transaction_type', 'income')->count();
    $expenses = Transaction::where('user_id', auth()->user()->id)->where('transaction_type', 'expense')->count();

    // expenses total percentage


    return response()->json([
      'transfers' => $transfers,
      'incomes' => $incomes,
      'expenses' => $expenses
    ]);
  }
}
