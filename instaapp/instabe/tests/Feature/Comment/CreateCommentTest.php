<?php

use App\Models\Post;
use App\Models\User;

test('authenticated user can create a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/posts/{$post->id}/comments", [
            'body' => 'Nice post!',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'body', 'user', 'post_id', 'created_at'],
        ]);

    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'post_id' => $post->id,
        'body' => 'Nice post!',
    ]);
});

test('comment body is required', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/posts/{$post->id}/comments", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['body']);
});

test('unauthenticated user cannot create a comment', function () {
    $post = Post::factory()->create();

    $response = $this->postJson("/api/posts/{$post->id}/comments", [
        'body' => 'Nice post!',
    ]);

    $response->assertUnauthorized();
});
