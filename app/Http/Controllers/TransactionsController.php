<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;


class TransactionsController extends Controller
{
    public function index()
    {

        $data = Post::query()
      ->with('user:id, name, email, password')
      ->orderBy('created_at', 'desc')
      ->get();

    return PostResource::collection($data);

    }

    public function store(PostRequest $request)
    {
      // Create a new transfert
      $post = Post::create([
        'amount' => $request->title,
        'from' => $request->content,
        'to' => $request->content,
        'note' => $request->content,
        'description' => $request->content,
        'user_id' => auth()->id()
      ]);
  
      return PostResource::make($post);
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

  public function destroy(Post $post)
  {
    $post->delete();

    return response()->noContent();
  }

}
