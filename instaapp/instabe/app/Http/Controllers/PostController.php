<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Jobs\FanOutPostJob;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = DB::transaction(function () use ($request): Post {
            $post = $request->user()->posts()->create([
                'text' => $request->validated('text'),
            ]);

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('post-images', 'public');

                $post->images()->create([
                    'image_path' => $path,
                    'order' => $index,
                ]);
            }

            return $post;
        });

        FanOutPostJob::dispatch($post);

        $post->load('user', 'images');
        $post->loadCount('likes', 'comments');
        $post->setAttribute('liked_by_me', false);

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Post $post): PostResource
    {
        $post->load('user', 'images');
        $post->loadCount('likes', 'comments');
        $post->setAttribute('liked_by_me', $post->likes()->where('user_id', $request->user()->id)->exists());

        return new PostResource($post);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $post->delete();

        return response()->json(null, 204);
    }
}
