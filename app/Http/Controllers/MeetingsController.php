<?php

namespace App\Http\Controllers;

use App\Http\Requests\Meetings\CreateMeetingRequest;
use App\Http\Requests\Meetings\GetMeetingsRequest;
use App\Http\Requests\Meetings\UpdateMeetingRequest;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;

class MeetingsController extends Controller
{
    public function get(GetMeetingsRequest $request): JsonResponse
    {
        $query = Meeting::with(['offer', 'person']);

        if ($status = $request->query('status')) {
            if ($status === 'upcoming') {
                $query->where('occurs_at', '>=', now());
            } else {
                $query->where('occurs_at', '<', now());
            }
        }

        $sortDir = $request->query('sort_dir', 'asc');
        $meetings = $query->orderBy('occurs_at', $sortDir)->get();

        return response()->json($meetings);
    }

    public function create(CreateMeetingRequest $request): JsonResponse
    {
        $meeting = Meeting::create($request->validated());

        return response()->json($meeting->load(['offer', 'person']), 201);
    }

    public function update(UpdateMeetingRequest $request, Meeting $meeting): JsonResponse
    {
        $meeting->update($request->validated());

        return response()->json($meeting->fresh()->load(['offer', 'person']));
    }

    public function delete(Meeting $meeting): JsonResponse
    {
        $meeting->delete();

        return response()->json(null, 204);
    }
}
