<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'last_page' => $activities->lastPage(),
            ],
        ]);
    }

    public function show(Activity $activity): JsonResponse
    {
        $activity->incrementViewCount();
        $activity->load([
            'user',
            'confirmedRegistrations.user',
            'waitlistRegistrations.user',
            'group',
            'photos',
            'settlement',
        ]);

        return response()->json([
            'data' => $activity,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => ['required', Rule::in(['badminton', 'book_club', 'parent_child_market', 'other'])],
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'max_participants' => 'required|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'fee_description' => 'nullable|string',
        ]);

        $activity = Activity::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'max_participants' => $validated['max_participants'],
            'fee' => $validated['fee'] ?? 0,
            'fee_description' => $validated['fee_description'] ?? null,
            'status' => 'recruiting',
        ]);

        $group = ActivityGroup::create([
            'activity_id' => $activity->id,
            'name' => $activity->title . ' 活动群',
            'description' => $activity->description,
        ]);

        $group->addMember($user->id, 'owner');

        $activity->load('user');

        return response()->json([
            'data' => $activity,
            'message' => '活动发布成功',
        ], 201);
    }

    public function update(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => ['sometimes', Rule::in(['badminton', 'book_club', 'parent_child_market', 'other'])],
            'location' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_time' => 'sometimes|date',
            'end_time' => 'nullable|date|after:start_time',
            'max_participants' => 'sometimes|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'fee_description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['draft', 'recruiting', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $activity->update($validated);

        if ($activity->group && isset($validated['title'])) {
            $activity->group->update(['name' => $validated['title'] . ' 活动群']);
        }

        $activity->load('user');

        return response()->json([
            'data' => $activity,
            'message' => '活动更新成功',
        ]);
    }

    public function destroy(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $activity->delete();

        return response()->json([
            'message' => '活动删除成功',
        ]);
    }

    public function myActivities(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $activities = Activity::where('user_id', $user->id)
            ->with('confirmedRegistrations')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'last_page' => $activities->lastPage(),
            ],
        ]);
    }

    public function myJoinedActivities(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $registrations = $user->activityRegistrations()
            ->with('activity.user', 'activity.confirmedRegistrations')
            ->whereIn('status', ['confirmed', 'waitlist'])
            ->orderBy('registered_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $registrations->items(),
            'meta' => [
                'current_page' => $registrations->currentPage(),
                'per_page' => $registrations->perPage(),
                'total' => $registrations->total(),
                'last_page' => $registrations->lastPage(),
            ],
        ]);
    }
}
