<?php

use App\Models\Post;
use App\Models\User;

test('user can delete their own post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->deleteJson("/api/posts/{$post->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('user cannot delete another users post', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)
        ->deleteJson("/api/posts/{$post->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});

test('unauthenticated user cannot delete a post', function () {
    $post = Post::factory()->create();

    $response = $this->deleteJson("/api/posts/{$post->id}");

    $response->assertUnauthorized();
});
