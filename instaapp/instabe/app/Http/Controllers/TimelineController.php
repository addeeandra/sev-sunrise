<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimelineEntryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TimelineController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $entries = $request->user()
            ->timelineEntries()
            ->with(['post' => function ($query): void {
                $query->with('user', 'images')
                    ->withCount('likes', 'comments')
                    ->withLikedByMe();
            }])
            ->orderByDesc('id')
            ->cursorPaginate(15);

        return TimelineEntryResource::collection($entries);
    }
}
