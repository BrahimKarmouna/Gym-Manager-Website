<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;


class TransactionController extends Controller
{
  public function index()
  {

    $data = Transaction::query()
      ->orderBy("id", "desc")
      ->get();

    return TransactionResource::collection($data);
  }

  public function store(Request $request)
  {
    // Create a new transaction
    $transaction = Transaction::create([
      'amount' => $request->amount,
      'source_account_id' => $request->from,
      'destination_account_id' => $request->to,
      'category_id' => $request->category,
      'note' => $request->note,
      'transaction_type' => $request->type,
      // 'description' => $request->content,
      'user_id' => auth()->id()
    ]);


    // $transaction = Transaction::create([
    //   //  'amount' => 2,
    //   //   'source_account_id' =>4,
    //   //   'destination_account_id' => 2,
    //   //   'category_id' => 1,
    //   //   'note' => 'hhhh',
    //   //   'transaction_type' => 8,
    //   //   // 'description' => $request->content,
    //   //   'user_id' => auth()->id()
    // ]);
    return TransactionResource::make($transaction);
  }

  public function show(Post $post)
  {
    return PostResource::make($post);
  }

  public function update(PostRequest $request, Post $post)
  {
    // Update the existing transaction
    $post->update([
      'amount' => $request->title,
      'from' => $request->content,
      'to' => $request->content,
      'note' => $request->content,
      'description' => $request->content
    ]);



    return PostResource::make($post);
  }

  public function destroy(Transaction $transaction)
  {
    $transaction->delete();

    return response()->noContent();
  }

}
