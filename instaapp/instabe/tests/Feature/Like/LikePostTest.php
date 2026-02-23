<?php

use App\Models\Post;
use App\Models\User;

test('user can like a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/posts/{$post->id}/like");

    $response->assertOk()
        ->assertJson([
            'liked' => true,
            'likes_count' => 1,
        ]);

    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('user can unlike a post by liking it again', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    // Like
    $this->actingAs($user)->postJson("/api/posts/{$post->id}/like");

    // Unlike
    $response = $this->actingAs($user)
        ->postJson("/api/posts/{$post->id}/like");

    $response->assertOk()
        ->assertJson([
            'liked' => false,
            'likes_count' => 0,
        ]);

    $this->assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('multiple users can like the same post', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user1)->postJson("/api/posts/{$post->id}/like");
    $response = $this->actingAs($user2)->postJson("/api/posts/{$post->id}/like");

    $response->assertOk()
        ->assertJson(['likes_count' => 2]);
});
