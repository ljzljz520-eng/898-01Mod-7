<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->middleware('auth:sanctum');
});

// Topics routes
Route::apiResource('topics', App\Http\Controllers\Api\TopicController::class)->except(['store', 'update', 'destroy']);
Route::post('topics', [App\Http\Controllers\Api\TopicController::class, 'store'])->middleware('auth:sanctum');
Route::put('topics/{topic}', [App\Http\Controllers\Api\TopicController::class, 'update'])->middleware('auth:sanctum');
Route::patch('topics/{topic}', [App\Http\Controllers\Api\TopicController::class, 'update'])->middleware('auth:sanctum');
Route::delete('topics/{topic}', [App\Http\Controllers\Api\TopicController::class, 'destroy'])->middleware('auth:sanctum');

// Topic replies routes
Route::get('topics/{topic}/replies', [App\Http\Controllers\Api\ReplyController::class, 'index']);
Route::post('topics/{topic}/replies', [App\Http\Controllers\Api\ReplyController::class, 'store'])->middleware('auth:sanctum');

// Replies routes (for update/delete)
Route::apiResource('replies', App\Http\Controllers\Api\ReplyController::class)->except(['index', 'store']);

// Activities routes
Route::apiResource('activities', App\Http\Controllers\Api\ActivityController::class)->except(['store', 'update', 'destroy']);
Route::post('activities', [App\Http\Controllers\Api\ActivityController::class, 'store'])->middleware('auth:sanctum');
Route::put('activities/{activity}', [App\Http\Controllers\Api\ActivityController::class, 'update'])->middleware('auth:sanctum');
Route::patch('activities/{activity}', [App\Http\Controllers\Api\ActivityController::class, 'update'])->middleware('auth:sanctum');
Route::delete('activities/{activity}', [App\Http\Controllers\Api\ActivityController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('my/activities', [App\Http\Controllers\Api\ActivityController::class, 'myActivities'])->middleware('auth:sanctum');
Route::get('my/joined-activities', [App\Http\Controllers\Api\ActivityController::class, 'myJoinedActivities'])->middleware('auth:sanctum');

// Activity Registration routes
Route::get('activities/{activity}/registrations', [App\Http\Controllers\Api\ActivityRegistrationController::class, 'index'])->middleware('auth:sanctum');
Route::post('activities/{activity}/register', [App\Http\Controllers\Api\ActivityRegistrationController::class, 'store'])->middleware('auth:sanctum');
Route::post('activities/{activity}/cancel-registration', [App\Http\Controllers\Api\ActivityRegistrationController::class, 'cancel'])->middleware('auth:sanctum');
Route::get('activities/{activity}/registration-status', [App\Http\Controllers\Api\ActivityRegistrationController::class, 'checkStatus'])->middleware('auth:sanctum');
Route::post('activities/{activity}/registrations/{registration}/mark-attended', [App\Http\Controllers\Api\ActivityRegistrationController::class, 'markAttended'])->middleware('auth:sanctum');

// Activity Group routes
Route::get('activities/{activity}/group', [App\Http\Controllers\Api\ActivityGroupController::class, 'show'])->middleware('auth:sanctum');
Route::get('activities/{activity}/group/messages', [App\Http\Controllers\Api\ActivityGroupController::class, 'getMessages'])->middleware('auth:sanctum');
Route::post('activities/{activity}/group/messages', [App\Http\Controllers\Api\ActivityGroupController::class, 'sendMessage'])->middleware('auth:sanctum');
Route::get('activities/{activity}/group/members', [App\Http\Controllers\Api\ActivityGroupController::class, 'getMembers'])->middleware('auth:sanctum');
Route::put('activities/{activity}/group', [App\Http\Controllers\Api\ActivityGroupController::class, 'updateGroup'])->middleware('auth:sanctum');
Route::delete('activities/{activity}/group/members/{userId}', [App\Http\Controllers\Api\ActivityGroupController::class, 'removeMember'])->middleware('auth:sanctum');
Route::get('my/activity-groups', [App\Http\Controllers\Api\ActivityGroupController::class, 'myGroups'])->middleware('auth:sanctum');

// Activity Photos & Settlement routes
Route::get('activities/{activity}/photos', [App\Http\Controllers\Api\ActivityMediaController::class, 'getPhotos']);
Route::post('activities/{activity}/photos', [App\Http\Controllers\Api\ActivityMediaController::class, 'uploadPhoto'])->middleware('auth:sanctum');
Route::delete('activities/{activity}/photos/{photo}', [App\Http\Controllers\Api\ActivityMediaController::class, 'deletePhoto'])->middleware('auth:sanctum');
Route::post('activities/{activity}/photos/reorder', [App\Http\Controllers\Api\ActivityMediaController::class, 'reorderPhotos'])->middleware('auth:sanctum');
Route::get('activities/{activity}/settlement', [App\Http\Controllers\Api\ActivityMediaController::class, 'getSettlement']);
Route::post('activities/{activity}/settlement', [App\Http\Controllers\Api\ActivityMediaController::class, 'createOrUpdateSettlement'])->middleware('auth:sanctum');
Route::post('activities/{activity}/settlement/submit', [App\Http\Controllers\Api\ActivityMediaController::class, 'submitSettlement'])->middleware('auth:sanctum');
Route::post('activities/{activity}/settlement/approve', [App\Http\Controllers\Api\ActivityMediaController::class, 'approveSettlement'])->middleware('auth:sanctum');
Route::post('activities/{activity}/settlement/reject', [App\Http\Controllers\Api\ActivityMediaController::class, 'rejectSettlement'])->middleware('auth:sanctum');

// （原 Admin 后台 API 路由已移除）
