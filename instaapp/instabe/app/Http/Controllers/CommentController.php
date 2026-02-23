<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): CommentResource
    {
        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        $comment->load('user');

        return new CommentResource($comment);
    }

    public function index(Post $post): AnonymousResourceCollection
    {
        $comments = $post->comments()
            ->with('user')
            ->orderBy('id')
            ->cursorPaginate(15);

        return CommentResource::collection($comments);
    }
}
