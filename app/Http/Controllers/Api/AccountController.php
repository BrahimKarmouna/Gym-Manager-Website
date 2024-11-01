<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountRessource;
use App\Models\Account;
use App\Enums\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    // Store a new account
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

        return response()->json($account, 201);
    }

    // Retrieve all accounts
    public function index()
    {
        $accounts = Account::latest()->get();
        return response()->json($accounts);
    }

    // Retrieve a specific account
    public function show($id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json($account);
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
