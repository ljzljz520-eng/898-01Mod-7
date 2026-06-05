<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivityMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityGroupController extends Controller
{
    public function show(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $group = $activity->group;
        if (!$group) {
            return response()->json(['message' => '活动群不存在'], 404);
        }

        if (!$group->isMember($user->id) && $activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限访问该群聊'], 403);
        }

        $group->load(['members.user']);

        return response()->json([
            'data' => $group,
        ]);
    }

    public function getMessages(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $group = $activity->group;
        if (!$group) {
            return response()->json(['message' => '活动群不存在'], 404);
        }

        if (!$group->isMember($user->id) && !$user->isAdmin()) {
            return response()->json(['message' => '无权限访问该群聊'], 403);
        }

        $query = $group->messages()->with('user');

        if ($request->has('before_id')) {
            $query->where('id', '<', $request->before_id);
        }

        if ($request->has('after_id')) {
            $query->where('id', '>', $request->after_id);
        }

        $messages = $query->orderBy('sent_at', 'desc')
            ->paginate($request->get('per_page', 50));

        return response()->json([
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'last_page' => $messages->lastPage(),
            ],
        ]);
    }

    public function sendMessage(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $group = $activity->group;
        if (!$group) {
            return response()->json(['message' => '活动群不存在'], 404);
        }

        if (!$group->isMember($user->id)) {
            return response()->json(['message' => '不是群成员，无法发送消息'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'sometimes|in:text,image',
        ]);

        $message = ActivityMessage::create([
            'activity_group_id' => $group->id,
            'user_id' => $user->id,
            'type' => $validated['type'] ?? 'text',
            'content' => $validated['content'],
        ]);

        $message->load('user');

        return response()->json([
            'data' => $message,
            'message' => '消息发送成功',
        ], 201);
    }

    public function getMembers(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $group = $activity->group;
        if (!$group) {
            return response()->json(['message' => '活动群不存在'], 404);
        }

        if (!$group->isMember($user->id) && !$user->isAdmin()) {
            return response()->json(['message' => '无权限访问'], 403);
        }

        $members = $group->members()->with('user')->get();

        return response()->json([
            'data' => $members,
        ]);
    }

    public function updateGroup(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $group = $activity->group;
        if (!$group) {
            return response()->json(['message' => '活动群不存在'], 404);
        }

        $member = $group->members()->where('user_id', $user->id)->first();
        if (!$member || !$member->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'avatar' => 'sometimes|string',
        ]);

        $group->update($validated);

        return response()->json([
            'data' => $group,
            'message' => '群信息更新成功',
        ]);
    }

    public function removeMember(Request $request, Activity $activity, $userId): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $group = $activity->group;
        if (!$group) {
            return response()->json(['message' => '活动群不存在'], 404);
        }

        $member = $group->members()->where('user_id', $user->id)->first();
        if (!$member || !$member->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        if ($userId == $user->id) {
            return response()->json(['message' => '不能移除自己'], 400);
        }

        $removedMember = $group->members()->where('user_id', $userId)->first();
        if (!$removedMember) {
            return response()->json(['message' => '该用户不是群成员'], 404);
        }

        if ($removedMember->isOwner()) {
            return response()->json(['message' => '不能移除群主'], 403);
        }

        $group->removeMember($userId);

        $group->messages()->create([
            'user_id' => $user->id,
            'type' => 'system',
            'content' => "{$removedMember->user->username} 被移出了活动群",
        ]);

        return response()->json([
            'message' => '移除成功',
        ]);
    }

    public function myGroups(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        $groups = $user->activityGroups()
            ->with('activity.user')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $groups->items(),
            'meta' => [
                'current_page' => $groups->currentPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
                'last_page' => $groups->lastPage(),
            ],
        ]);
    }
}
