<?php

use Illuminate\Support\Facades\Route;

// 首页重定向到主题列表
Route::get('/', function () {
    return redirect()->route('topics.index');
});

// 前台认证路由
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 主题路由
Route::resource('topics', App\Http\Controllers\TopicController::class);
Route::post('topics/{topic}/replies', [App\Http\Controllers\ReplyController::class, 'store'])->name('replies.store')->middleware('auth');
Route::delete('replies/{reply}', [App\Http\Controllers\ReplyController::class, 'destroy'])->name('replies.destroy')->middleware('auth');

// 同城活动路由
Route::group(['prefix' => 'activities', 'as' => 'activities.'], function () {
    Route::get('/', [App\Http\Controllers\ActivityController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ActivityController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [App\Http\Controllers\ActivityController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/my', [App\Http\Controllers\ActivityController::class, 'my'])->name('my')->middleware('auth');
    Route::get('/joined', [App\Http\Controllers\ActivityController::class, 'joined'])->name('joined')->middleware('auth');
    Route::get('/{activity}', [App\Http\Controllers\ActivityController::class, 'show'])->name('show');
    Route::get('/{activity}/edit', [App\Http\Controllers\ActivityController::class, 'edit'])->name('edit')->middleware('auth');
    Route::put('/{activity}', [App\Http\Controllers\ActivityController::class, 'update'])->name('update')->middleware('auth');
    Route::delete('/{activity}', [App\Http\Controllers\ActivityController::class, 'destroy'])->name('destroy')->middleware('auth');
    Route::post('/{activity}/register', [App\Http\Controllers\ActivityController::class, 'register'])->name('register')->middleware('auth');
    Route::post('/{activity}/cancel', [App\Http\Controllers\ActivityController::class, 'cancelRegistration'])->name('cancel')->middleware('auth');
    Route::get('/{activity}/group', [App\Http\Controllers\ActivityController::class, 'group'])->name('group')->middleware('auth');
    Route::post('/{activity}/send-message', [App\Http\Controllers\ActivityController::class, 'sendMessage'])->name('send-message')->middleware('auth');
});
