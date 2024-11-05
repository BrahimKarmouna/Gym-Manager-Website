<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Enums\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{

  public function index(Request $request)
  {
    $accounts = Account::latest()->get();

    $total = $accounts->count();

    $totalBalance = $accounts->sum('balance');

    return AccountResource::collection($accounts)->additional([
      'total' => $total,
      'totalBalance' => $totalBalance
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

  // Retrieve all accounts

  // Retrieve a specific account
  public function show(Account $account)
  {
    $account->load('incomes', 'expenses', 'transfers');
    return AccountResource::make($account);
  }

  // Update a specific account
  public function update(Request $request, $id)
  {
    $account = Account::find($id);

    if (!$account) {
      return response()->json(['message' => 'Account not found'], 404);
    }

    $validator = Validator::make($request->all(), [
      'name' => 'sometimes|required|string|max:255',
      'rib' => 'sometimes|required|string|max:128|unique:accounts,rib,' . $account->id,
      'balance' => 'sometimes|required|numeric',
      'account_type' => 'sometimes|required|in:' . implode(',', AccountType::cases()),
      'total_expense' => 'nullable|numeric',
      'total_income' => 'nullable|numeric',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

    // Update the account with the new data
    $account->update($request->all());

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
}
