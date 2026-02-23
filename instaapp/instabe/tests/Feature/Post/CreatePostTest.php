<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authenticated user can create a post with images', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/posts', [
            'text' => 'Hello world',
            'images' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg'),
            ],
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'text',
                'user' => ['id', 'name', 'email'],
                'images' => [['id', 'image_url', 'order']],
                'likes_count',
                'comments_count',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('posts', ['text' => 'Hello world', 'user_id' => $user->id]);
    $this->assertDatabaseCount('post_images', 2);
});

test('post creation requires at least one image', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/posts', [
            'text' => 'Hello world',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['images']);
});

test('post text is optional', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/posts', [
            'images' => [UploadedFile::fake()->image('photo.jpg')],
        ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('posts', ['text' => null, 'user_id' => $user->id]);
});

test('unauthenticated user cannot create a post', function () {
    $response = $this->postJson('/api/posts', [
        'text' => 'Hello',
    ]);

    $response->assertUnauthorized();
});
