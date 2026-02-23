<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Post $post): JsonResponse
    {
        $existingLike = $post->likes()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $post->likes()->create([
                'user_id' => $request->user()->id,
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
