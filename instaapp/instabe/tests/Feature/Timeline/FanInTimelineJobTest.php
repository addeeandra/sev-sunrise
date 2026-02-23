<?php

use App\Jobs\FanInTimelineJob;
use App\Models\Post;
use App\Models\User;

test('fan-in job backfills timeline with latest 100 posts', function () {
    Post::factory()->count(100)->create();
    $user = User::factory()->create();

    (new FanInTimelineJob($user))->handle();

    expect($user->timelineEntries()->count())->toBe(100);
});

test('fan-in job selects only the 100 most recent posts', function () {
    $posts = Post::factory()->count(120)->create();
    $user = User::factory()->create();

    (new FanInTimelineJob($user))->handle();

    expect($user->timelineEntries()->count())->toBe(100);

    $oldestPostIds = $posts->sortBy('id')->take(20)->pluck('id');
    $timelinePostIds = $user->timelineEntries()->pluck('post_id');

    expect($timelinePostIds->intersect($oldestPostIds))->toHaveCount(0);
});

test('fan-in job handles zero posts gracefully', function () {
    $user = User::factory()->create();

    (new FanInTimelineJob($user))->handle();

    expect($user->timelineEntries()->count())->toBe(0);
});

test('fan-in job handles fewer than 100 posts', function () {
    Post::factory()->count(5)->create();
    $user = User::factory()->create();

    (new FanInTimelineJob($user))->handle();

    expect($user->timelineEntries()->count())->toBe(5);
});
