<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\TimelineEntry;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FanInTimelineJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $posts = Post::query()
            ->select('id')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        if ($posts->isEmpty()) {
            return;
        }

        $entries = $posts->map(fn (Post $post) => [
            'user_id' => $this->user->id,
            'post_id' => $post->id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        TimelineEntry::query()->insert($entries);
    }
}
