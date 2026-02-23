<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('comments can be listed for a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    Comment::factory()->count(3)->for($post)->create();

    $response = $this->actingAs($user)
        ->getJson("/api/posts/{$post->id}/comments");

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'body', 'user', 'post_id', 'created_at']],
        ]);
});

test('comments are cursor paginated', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    Comment::factory()->count(20)->for($post)->create();

    $response = $this->actingAs($user)
        ->getJson("/api/posts/{$post->id}/comments");

    $response->assertOk()
        ->assertJsonCount(15, 'data')
        ->assertJsonStructure([
            'data',
            'meta' => ['next_cursor', 'prev_cursor'],
        ]);
});

test('comments second page can be fetched with cursor', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    Comment::factory()->count(20)->for($post)->create();

    $firstPage = $this->actingAs($user)
        ->getJson("/api/posts/{$post->id}/comments");

    $nextCursor = $firstPage->json('meta.next_cursor');

    $secondPage = $this->actingAs($user)
        ->getJson("/api/posts/{$post->id}/comments?cursor={$nextCursor}");

    $secondPage->assertOk()
        ->assertJsonCount(5, 'data');
});
