<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;


class TransactionController extends Controller
{
  public function index(Request $request)
  {
    $data = Transaction::latest()
      ->where('transaction_type', $request->type)
      ->get();

    return TransactionResource::collection($data);
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
    return TransactionResource::make($transaction);
  }

  public function update(PostRequest $request, Post $post)
  {
    // Update the existing transaction
    $post->update([
      'date' => $request->date,
      'amount' => $request->title,
      'from' => $request->content,
      'to' => $request->content,
      'note' => $request->content,
      'description' => $request->content
    ]);

    return TransactionResource::make($post);
  }

  public function destroy(Transaction $transaction)
  {
    $transaction->delete();

    return response()->noContent();
  }

}
