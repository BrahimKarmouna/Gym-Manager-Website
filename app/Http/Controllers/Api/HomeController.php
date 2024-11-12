<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{


  public function index(Request $request)
  {
    $transfers = Transaction::where("user_id", auth()->user()->id)->where('transaction_type', 'transfer')->count();
    $incomes = Transaction::where('user_id', auth()->user()->id)->where('transaction_type', 'income')->count();
    $expenses = Transaction::where('user_id', auth()->user()->id)->where('transaction_type', 'expense')->count();

    $lastTransactions = Transaction::where('user_id', auth()->id())
      ->where('created_at', '>=', now()->subDays(7)) // Last 7 days
      ->latest()
      ->with('sourceAccount', 'destinationAccount')
      ->get();

    return response()->json([
      'transfers' => $transfers,
      'incomes' => $incomes,
      'expenses' => $expenses,
      'last_transactions' => $lastTransactions
    ]);
  }


  public function incomes(Request $request)
  {
    $selectedYear = $request->query('year', Carbon::now()->year);

    $categories = Category::query()
      ->select(['id', 'name'])
      ->selectSub(
        query: Transaction::query()
          ->selectRaw('COALESCE(SUM(amount), 0) AS total')
          ->where('transaction_type', TransactionType::INCOME)
          ->where('user_id', auth()->id())
          ->whereColumn('category_id', 'categories.id')
          ->whereYear('created_at', $selectedYear),
        as: 'total'
      )

      ->where('transaction_type', TransactionType::INCOME)
      ->toBase()
      ->get();

    // Get total amount of all incomes
    $totalIncome = $categories->pluck('total')->sum();

    // Calculate percentage for each category
    $chartData = $categories->map(function ($category) use ($totalIncome) {
      return [
        'label' => $category->name,
        'percentage' => $totalIncome === 0.0 ? $totalIncome : round(($category->total / $totalIncome) * 100, 2)
      ];
    });

    return response()->json([
      'chart_data' => $chartData,
      'total_incomes' => $totalIncome
    ]);
  }



  public function expenses(Request $request)
  {
    $selectedYear = $request->query('year', Carbon::now()->year);

    $categories = Category::query()
      ->select(['id', 'name'])
      ->selectSub(
        query: Transaction::query()
          ->selectRaw('COALESCE(SUM(amount), 0) AS total')
          ->where('transaction_type', TransactionType::EXPENSE)
          ->where('user_id', auth()->id())
          ->whereColumn('category_id', 'categories.id')
          ->whereYear('created_at', $selectedYear),
        as: 'total'
      )

      ->where('transaction_type', TransactionType::EXPENSE)
      ->toBase()
      ->get();

    // Get total amount of all incomes
    $totalExpense = $categories->pluck('total')->sum();

    // Calculate percentage for each category
    $chartData = $categories->map(function ($category) use ($totalExpense) {
      return [
        'label' => $category->name,
        'percentage' => $totalExpense === 0.0 ? $totalExpense : round(($category->total / $totalExpense) * 100, 2)
      ];
    });

    return response()->json([
      'chart_data' => $chartData,
      'total_expenses' => $totalExpense
    ]);
  }
}
