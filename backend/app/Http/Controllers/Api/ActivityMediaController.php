<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\ActivitySettlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityMediaController extends Controller
{
    public function getPhotos(Activity $activity): JsonResponse
    {
        $photos = $activity->photos()->with('user')->get();

        return response()->json([
            'data' => $photos,
        ]);
    }

    public function uploadPhoto(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限上传照片'], 403);
        }

        if ($activity->status !== 'completed') {
            return response()->json(['message' => '活动未结束，暂不能上传照片'], 400);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'caption' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $file = $request->file('photo');
        $path = $file->store('activity_photos/' . $activity->id, 'public');

        $photo = ActivityPhoto::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'image_path' => $path,
            'thumbnail_path' => $path,
            'caption' => $request->caption,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        $photo->load('user');

        return response()->json([
            'data' => $photo,
            'message' => '照片上传成功',
        ], 201);
    }

    public function deletePhoto(Request $request, Activity $activity, ActivityPhoto $photo): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($photo->activity_id !== $activity->id) {
            return response()->json(['message' => '照片不属于该活动'], 400);
        }

        if ($photo->user_id !== $user->id && $activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限删除'], 403);
        }

        if (Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $photo->delete();

        return response()->json([
            'message' => '照片删除成功',
        ]);
    }

    public function reorderPhotos(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:activity_photos,id',
            'photos.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->photos as $photoData) {
            $photo = ActivityPhoto::find($photoData['id']);
            if ($photo && $photo->activity_id === $activity->id) {
                $photo->update(['sort_order' => $photoData['sort_order']]);
            }
        }

        return response()->json([
            'message' => '排序更新成功',
        ]);
    }

    public function getSettlement(Activity $activity): JsonResponse
    {
        $settlement = $activity->settlement;

        return response()->json([
            'data' => $settlement,
        ]);
    }

    public function createOrUpdateSettlement(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $validated = $request->validate([
            'total_income' => 'required|numeric|min:0',
            'total_expense' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'expense_details' => 'nullable|array',
            'income_details' => 'nullable|array',
        ]);

        $settlement = $activity->settlement;

        if ($settlement) {
            if ($settlement->status === 'approved') {
                return response()->json(['message' => '已审核的结算不能修改'], 400);
            }
            $settlement->update(array_merge($validated, [
                'balance' => $validated['total_income'] - $validated['total_expense'],
            ]));
        } else {
            $settlement = ActivitySettlement::create(array_merge($validated, [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'balance' => $validated['total_income'] - $validated['total_expense'],
                'status' => 'draft',
            ]));
        }

        return response()->json([
            'data' => $settlement,
            'message' => '结算保存成功',
        ]);
    }

    public function submitSettlement(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if ($activity->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $settlement = $activity->settlement;
        if (!$settlement) {
            return response()->json(['message' => '请先创建结算'], 400);
        }

        if ($settlement->status !== 'draft') {
            return response()->json(['message' => '只有草稿状态可以提交'], 400);
        }

        $settlement->submit();

        return response()->json([
            'data' => $settlement,
            'message' => '结算已提交审核',
        ]);
    }

    public function approveSettlement(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if (!$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $settlement = $activity->settlement;
        if (!$settlement) {
            return response()->json(['message' => '结算不存在'], 404);
        }

        if ($settlement->status !== 'submitted') {
            return response()->json(['message' => '只有已提交的结算可以审核'], 400);
        }

        $settlement->approve();

        return response()->json([
            'data' => $settlement,
            'message' => '结算已审核通过',
        ]);
    }

    public function rejectSettlement(Request $request, Activity $activity): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '未认证'], 401);
        }

        if (!$user->isAdmin()) {
            return response()->json(['message' => '无权限操作'], 403);
        }

        $settlement = $activity->settlement;
        if (!$settlement) {
            return response()->json(['message' => '结算不存在'], 404);
        }

        if ($settlement->status !== 'submitted') {
            return response()->json(['message' => '只有已提交的结算可以审核'], 400);
        }

        $settlement->reject();

        return response()->json([
            'data' => $settlement,
            'message' => '结算已驳回',
        ]);
    }
}
