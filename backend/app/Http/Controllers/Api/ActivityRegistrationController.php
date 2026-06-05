<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityRegistrationController extends Controller
{
    public function index(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限查看'], 403);
        }

        $registrations = $activity->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

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

    public function store(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id === $user->id) {
            return response()->json(['message' => '发起人不能报名自己的活动'], 400);
        }

        if ($activity->status !== 'recruiting') {
            return response()->json(['message' => '活动不在招募中'], 400);
        }

        $existing = ActivityRegistration::where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'cancelled') {
                $existing->restore();
            } else {
                return response()->json(['message' => '已报名该活动'], 400);
            }
        }

        $hasSpots = $activity->hasAvailableSpots();
        $status = $hasSpots ? 'confirmed' : 'waitlist';
        $waitlistPosition = $hasSpots ? null : $activity->getWaitlistNextPosition();

        $registration = ActivityRegistration::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'status' => $status,
            'waitlist_position' => $waitlistPosition,
            'note' => $request->note,
        ]);

        if ($status === 'confirmed' && $activity->group) {
            $activity->group->addMember($user->id, 'member');

            $activity->group->messages()->create([
                'user_id' => $user->id,
                'type' => 'system',
                'content' => "{$user->username} 加入了活动群",
            ]);
        }

        $registration->load('user');

        return response()->json([
            'data' => $registration,
            'message' => $status === 'confirmed' ? '报名成功' : '已加入候补名单，当前顺位：' . $waitlistPosition,
        ], 201);
    }

    public function cancel(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $registration = ActivityRegistration::where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'waitlist'])
            ->first();

        if (!$registration) {
            return response()->json(['message' => '未找到报名记录'], 404);
        }

        $wasConfirmed = $registration->status === 'confirmed';
        $registration->cancel();

        if ($wasConfirmed && $activity->group) {
            $activity->group->removeMember($user->id);

            $activity->group->messages()->create([
                'user_id' => $user->id,
                'type' => 'system',
                'content' => "{$user->username} 退出了活动群",
            ]);

            $this->promoteWaitlistToConfirmed($activity);
        } elseif ($registration->status === 'waitlist') {
            $this->rearrangeWaitlistPositions($activity, $registration->waitlist_position);
        }

        return response()->json([
            'message' => '取消报名成功',
        ]);
    }

    protected function promoteWaitlistToConfirmed(Activity $activity): void
    {
        $nextWaitlist = $activity->waitlistRegistrations()->first();

        if ($nextWaitlist) {
            $nextWaitlist->confirm();

            if ($activity->group) {
                $activity->group->addMember($nextWaitlist->user_id, 'member');

                $activity->group->messages()->create([
                    'user_id' => $nextWaitlist->user_id,
                    'type' => 'system',
                    'content' => "{$nextWaitlist->user->username} 从候补名单转正，加入活动群",
                ]);
            }

            $this->rearrangeWaitlistPositions($activity);
        }
    }

    protected function rearrangeWaitlistPositions(Activity $activity, $removedPosition = null): void
    {
        $waitlistRegistrations = $activity->waitlistRegistrations()->get();
        $newPosition = 1;

        foreach ($waitlistRegistrations as $reg) {
            if ($removedPosition && $reg->waitlist_position === $removedPosition) {
                continue;
            }
            $reg->waitlist_position = $newPosition++;
            $reg->save();
        }
    }

    public function checkStatus(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $registration = ActivityRegistration::where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'data' => $registration ? [
                'status' => $registration->status,
                'waitlist_position' => $registration->waitlist_position,
                'registered_at' => $registration->registered_at,
            ] : null,
            'has_available_spots' => $activity->hasAvailableSpots(),
            'confirmed_count' => $activity->confirmedRegistrations()->count(),
            'waitlist_count' => $activity->waitlistRegistrations()->count(),
        ]);
    }

    public function markAttended(Request $request, Activity $activity, ActivityRegistration $registration): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        if ($registration->activity_id !== $activity->id) {
            return response()->json(['message' => '报名记录不匹配'], 400);
        }

        $registration->update(['status' => 'attended']);

        return response()->json([
            'message' => '已标记为已参加',
        ]);
    }
}
