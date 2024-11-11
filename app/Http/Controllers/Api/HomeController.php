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
    $selectedYear = $request->query('year', Carbon::now()->year) ?? date('Y');

    // Assuming a relationship where `category_id` is the foreign key in the `transactions` table
    $incomes = Transaction::where('transaction_type', TransactionType::INCOME)
      ->whereYear('created_at', $selectedYear) // filter by year
      ->get();

    // Now, we need to group by category and calculate the total amount per category
    $categorySums = $incomes->groupBy('category_id')->map(function ($group) {
      return $group->sum('amount'); // Assuming 'amount' is the field storing the transaction amount
    });

    // Get total amount of all incomes
    $totalIncome = $categorySums->sum();

    // Calculate percentage for each category
    $categoryPercentages = $categorySums->map(function ($sum) use ($totalIncome) {
      return ($sum / $totalIncome) * 100;
    });

    // Get the category names as well
    $categories = Category::whereIn('id', $categorySums->keys())->get()->keyBy('id');

    // Prepare data for chart
    $chartData = $categoryPercentages->map(function ($percentage, $categoryId) use ($categories) {
      return [
        'label' => $categories[$categoryId]->name, // Assuming 'name' is the category name
        'percentage' => round($percentage, 2)
      ];
    });

    return response()->json($chartData);
  }

  public function expenses(Request $request)
  {
    $selectedYear = $request->query('year', Carbon::now()->year) ?? date('Y');

    // Assuming a relationship where `category_id` is the foreign key in the `transactions` table
    $incomes = Transaction::where('transaction_type', TransactionType::EXPENSE)
      ->whereYear('created_at', $selectedYear) // filter by year
      ->get();

    // Now, we need to group by category and calculate the total amount per category
    $categorySums = $incomes->groupBy('category_id')->map(function ($group) {
      return $group->sum('amount'); // Assuming 'amount' is the field storing the transaction amount
    });

    // Get total amount of all incomes
    $totalIncome = $categorySums->sum();

    // Calculate percentage for each category
    $categoryPercentages = $categorySums->map(function ($sum) use ($totalIncome) {
      return ($sum / $totalIncome) * 100;
    });

    // Get the category names as well
    $categories = Category::whereIn('id', $categorySums->keys())->get()->keyBy('id');

    // Prepare data for chart
    $chartData = $categoryPercentages->map(function ($percentage, $categoryId) use ($categories) {
      return [
        'label' => $categories[$categoryId]->name, // Assuming 'name' is the category name
        'percentage' => round($percentage, 2)
      ];
    });

    return response()->json($chartData);
  }
}
