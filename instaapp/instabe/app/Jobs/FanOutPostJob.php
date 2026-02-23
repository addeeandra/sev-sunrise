<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\TimelineEntry;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FanOutPostJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        User::query()
            ->select('id')
            ->chunkById(500, function ($users): void {
                $entries = $users->map(fn (User $user) => [
                    'user_id' => $user->id,
                    'post_id' => $this->post->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                TimelineEntry::query()->insert($entries);
            });
    }
}
