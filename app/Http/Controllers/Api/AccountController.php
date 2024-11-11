<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{

  public function index(Request $request)
  {
    $accounts = Account::latest()->where('user_id', auth()->id())->with('incomes', 'expenses', 'transfers')->get();

    $total = $accounts->count();

    $total = $accounts->count();
    $totalBalance = $accounts->sum('balance');

    // Calculate total income and total expense across all accounts
    $totalIncome = $accounts->sum(function (Account $account) {
      return $account->incomes->sum('amount');  // Sum of all incomes for each account
    });

    $totalExpense = $accounts->sum(function ($account) {
      return $account->expenses->sum('amount');  // Sum of all expenses for each account
    });

    return AccountResource::collection($accounts)->additional([
      'total' => $total,
      'totalBalance' => $totalBalance,
      'totalIncome' => $totalIncome,
      'totalExpense' => $totalExpense
    ]);
  }

  public function store(AccountRequest $request)
  {
    // Create a new account
    $account = Account::create([
      'name' => $request->name,
      'balance' => $request->balance,
      'account_type' => $request->account_type,
      'rib' => $request->rib,
      'user_id' => auth()->id()
    ]);

    return AccountResource::make($account);
  }

  // Retrieve a specific account
  public function show(Account $account)
  {
    $account->load('incomes', 'expenses', 'transfers');
    $account->loadSum(['incomes', 'expenses'], 'amount');

    return AccountResource::make($account);
  }


  // Update a specific account
  public function update(UpdateAccountRequest $request, $id)
  {
    $account = Account::find($id);

    if (!$account) {
      return response()->json(['message' => 'Account not found'], 404);
    }

    // Update the account with the new data
    $account->update([
      'name' => $request->name,
      'balance' => $request->balance,
      'account_type' => $request->account_type ?? $account->account_type,
      'rib' => $request->rib
    ]);

    return response()->json($account);
  }

  // Delete a specific account
  public function destroy($id)
  {
    $account = Account::find($id);

    if (!$account) {
      return response()->json(['message' => 'Account not found'], 404);
    }

    $account->delete();

    return response()->json(['message' => 'Account deleted successfully']);
  }

  public function transfers($id)
  {
    $transfers = Account::find($id)->transfers()->with(['sourceAccount', 'destinationAccount'])->get();

    return TransactionResource::collection($transfers);
  }

  public function incomes($id)
  {
    $incomes = Account::find($id)->incomes()->with(['sourceAccount'])->get();

    return TransactionResource::collection($incomes);
  }

  public function expenses($id)
  {
    $expenses = Account::find($id)->expenses()->with(['sourceAccount'])->get();

    return TransactionResource::collection($expenses);
  }
}
