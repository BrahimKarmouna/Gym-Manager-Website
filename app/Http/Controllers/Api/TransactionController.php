<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;



class TransactionController extends Controller
{
  public function index(Request $request)
  {
    $data = Transaction::latest()
      ->with('sourceAccount', 'destinationAccount')
      ->where('user_id', auth()->id())
      ->when(
        $request->filled('search'),
        function ($query) use ($request) {
          $query->where(function ($query) use ($request) {
            $query->whereRelation('sourceAccount', 'name', 'like', '%' . $request->search . '%');
          });
        }
      )
      ->when(
        $request->get('transaction_type'),
        function ($query) use ($request) {
          $query->where('transaction_type', $request->transaction_type);
        }
      )
      ->get();

    $incomes = $data->where('transaction_type', TransactionType::INCOME->value)->sum("amount");
    $expenses = $data->where('transaction_type', TransactionType::EXPENSE->value)->sum("amount");
    $totalBalance = $data->sum('amount');

    return TransactionResource::collection($data)->additional([
      'incomes' => $incomes,
      'expenses' => $expenses,
      'totalBalance' => $totalBalance
    ]);
  }

  public function store(TransactionRequest $request)
  {
    $sourceAccount = Account::find($request->source_account_id);
    $destinationAccount = Account::find($request->destination_account_id);

    switch ($request->transaction_type) {
      case TransactionType::TRANSFER->value:
        $transaction = Transaction::create([
          'date' => $request->date('date'),
          'amount' => $request->amount,
          'source_account_id' => $request->source_account_id,
          'destination_account_id' => $request->destination_account_id,
          'category_id' => $request->category_id,
          'note' => $request->note,
          'transaction_type' => $request->transaction_type,
          'user_id' => auth()->id()
        ]);

        $sourceAccount->balance -= $request->amount;
        $destinationAccount->balance += $request->amount;
        $sourceAccount->save();
        $destinationAccount->save();
        break;

      case TransactionType::INCOME->value:
        $transaction = Transaction::create([
          'date' => $request->date('date'),
          'amount' => $request->amount,
          'category_id' => $request->category_id,
          'note' => $request->note,
          'source_account_id' => $request->source_account_id,
          'transaction_type' => $request->transaction_type,
          'user_id' => auth()->id()
        ]);

        $sourceAccount->balance += $request->amount;
        $sourceAccount->save();
        break;

      case TransactionType::EXPENSE->value:
        $transaction = Transaction::create([
          'date' => $request->date('date'),
          'amount' => $request->amount,
          'category_id' => $request->category_id,
          'note' => $request->note,
          'source_account_id' => $request->source_account_id,
          'transaction_type' => $request->transaction_type,
          'user_id' => auth()->id()
        ]);

        $sourceAccount->balance -= $request->amount;
        $sourceAccount->save();
        break;
    }

    return TransactionResource::make($transaction);
  }

  public function show(Transaction $transaction)
  {
    $transaction->load('sourceAccount', 'destinationAccount');
    return TransactionResource::make($transaction);
  }

  public function update(UpdateTransactionRequest $request, Transaction $transaction)
  {
    $sourceAccount = Account::find($transaction->source_account_id);
    $destinationAccount = Account::find($transaction->destination_account_id);

    $oldAmount = $transaction->amount;
    $newAmount = $request->amount;
    $difference = $newAmount - $oldAmount;

    switch ($transaction->transaction_type) {
      case TransactionType::TRANSFER->value:
        if ($difference > 0) {

          $sourceAccount->balance -= $difference;
          $destinationAccount->balance += $difference;

        } elseif ($difference < 0) {

          $sourceAccount->balance += abs($difference);
          $destinationAccount->balance -= abs($difference);

        }

        $sourceAccount->save();
        $destinationAccount->save();
        break;

      case TransactionType::INCOME->value:
        if ($difference != 0) {

          $sourceAccount->balance += $difference;
          $sourceAccount->save();

        }

        break;

      case TransactionType::EXPENSE->value:
        if ($difference != 0) {

          $sourceAccount->balance -= $difference;
          $sourceAccount->save();

        }
        break;
    }

    // Update the transaction with the new values
    $transaction->update([
      'date' => $request->date('date'),
      'amount' => $newAmount,
      'source_account_id' => $request->source_account_id ?? $transaction->source_account_id,
      'destination_account_id' => $request->destination_account_id ?? $transaction->destination_account_id,
      'category_id' => $request->category_id,
      'transaction_type' => $transaction->transaction_type,
      'note' => $request->note,
      'user_id' => auth()->id(),
    ]);

    return TransactionResource::make($transaction);
  }

  public function destroy(Transaction $transaction)
  {
    $transaction->delete();

    return response()->noContent();
  }

  public function dashboard(Request $request)
  {
    $expenses = Transaction::where('user_id', auth()->id())->where('transaction_type', TransactionType::EXPENSE)->sum('amount');
    $incomes = Transaction::where('user_id', auth()->id())->where('transaction_type', TransactionType::INCOME)->sum('amount');
    $totalBalance = Account::where('user_id', auth()->id())->sum('balance');

    return response()->json([
      'expenses' => $expenses,
      'incomes' => $incomes,
      'total_balance' => $totalBalance
    ]);
  }
}
