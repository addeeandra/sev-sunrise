<?php

use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;

test('authenticated user can view a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    PostImage::factory()->create(['post_id' => $post->id]);

    $response = $this->actingAs($user)
        ->getJson("/api/posts/{$post->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'text',
                'user' => ['id', 'name', 'email'],
                'images',
                'likes_count',
                'comments_count',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('unauthenticated user cannot view a post', function () {
    $post = Post::factory()->create();

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertUnauthorized();
});
