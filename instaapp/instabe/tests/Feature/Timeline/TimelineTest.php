<?php

use App\Models\Post;
use App\Models\TimelineEntry;
use App\Models\User;

test('user sees posts in their timeline', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    TimelineEntry::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/api/timeline');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'post', 'created_at']],
        ]);
});

test('timeline is cursor paginated', function () {
    $user = User::factory()->create();
    $posts = Post::factory()->count(20)->create();

    foreach ($posts as $post) {
        TimelineEntry::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    $response = $this->actingAs($user)
        ->getJson('/api/timeline');

    $response->assertOk()
        ->assertJsonCount(15, 'data')
        ->assertJsonStructure([
            'data',
            'meta' => ['next_cursor', 'prev_cursor'],
        ]);
});

test('timeline shows newest entries first', function () {
    $user = User::factory()->create();
    $oldPost = Post::factory()->create();
    $newPost = Post::factory()->create();

    TimelineEntry::factory()->create([
        'user_id' => $user->id,
        'post_id' => $oldPost->id,
    ]);
    TimelineEntry::factory()->create([
        'user_id' => $user->id,
        'post_id' => $newPost->id,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/api/timeline');

    $data = $response->json('data');
    expect($data[0]['post']['id'])->toBe($newPost->id);
    expect($data[1]['post']['id'])->toBe($oldPost->id);
});

test('unauthenticated user cannot access timeline', function () {
    $response = $this->getJson('/api/timeline');

    $response->assertUnauthorized();
});
