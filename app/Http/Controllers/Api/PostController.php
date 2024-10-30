<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use PDO;

class PostController extends Controller
{
  public function index()
  {
    // 1. Using PDO
    // $dns = "mysql:host=sql7.freemysqlhosting.net;dbname=sql7741525";

    // $username = "sql7741525";

    // $password = 'yEiFrKJjz8';

    // $pdo = new PDO($dns, $username, $password);

    // $statement = $pdo->query("SELECT * FROM posts WHERE user_id = 1");

    // $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    // return $data;

    // 2. Using Query Builder
    // $data = \DB::select('SELECT * FROM posts WHERE user_id = 1');

    // $data = \DB::table('posts')
    //   ->where('user_id', '=', '1')
    //   ->get();

    // return $data;

    // 3. Using Eloquent
    $data = Post::query()
      ->with('user:id,name,profile_photo_path')
      ->orderBy('created_at', 'desc')
      ->get();

    return PostResource::collection($data);
  }

  public function store(PostRequest $request)
  {
    // Create a new post
    $post = Post::create([
      'title' => $request->title,
      'content' => $request->content,
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
    // Update the existing post
    $post->update([
      'title' => $request->title,
      'content' => $request->content,
    ]);

    return PostResource::make($post);
  }

  public function destroy(Post $post)
  {
    $post->delete();

    return response()->noContent();
  }
}
