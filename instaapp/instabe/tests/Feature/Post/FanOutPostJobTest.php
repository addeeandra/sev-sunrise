<?php

use App\Jobs\FanOutPostJob;
use App\Models\Post;
use App\Models\User;

test('fan-out job creates timeline entries for all users', function () {
    User::factory()->count(5)->create();
    $author = User::factory()->create();
    $post = Post::factory()->for($author)->create();

    (new FanOutPostJob($post))->handle();

    // 5 created users + 1 author = 6 total users
    expect($post->timelineEntries()->count())->toBe(6);
});

test('fan-out job handles large number of users', function () {
    User::factory()->count(50)->create();
    $post = Post::factory()->create();

    (new FanOutPostJob($post))->handle();

    // 50 users + 1 post author = 51
    expect($post->timelineEntries()->count())->toBe(User::count());
});
