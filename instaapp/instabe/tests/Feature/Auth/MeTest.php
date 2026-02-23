<?php

use App\Models\User;

test('authenticated user can get their profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->getJson('/api/auth/me');

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('unauthenticated user cannot access profile', function () {
    $response = $this->getJson('/api/auth/me');

    $response->assertUnauthorized();
});
