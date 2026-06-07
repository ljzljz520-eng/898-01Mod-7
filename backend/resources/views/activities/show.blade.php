@extends('layouts.app')

@section('title', $activity->title)

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <span class="badge {{ activity_category_color($activity->category) }}">
            {{ activity_category_name($activity->category) }}
        </span>
        <span class="badge {{ activity_status_color($activity->status) }}">
            {{ activity_status_name($activity->status) }}
        </span>
    </div>
    
    <h1 class="text-3xl font-bold text-neutral-800 mb-4">{{ $activity->title }}</h1>
    
    <div class="flex flex-wrap items-center gap-4 text-sm text-neutral-500 mb-6">
        <span>发起人：{{ $activity->user->username }}</span>
        <span>发布时间：{{ $activity->created_at->format('Y-m-d H:i') }}</span>
        <span>浏览：{{ $activity->view_count }}</span>
        @auth
            @if($activity->user_id === auth()->id())
                <a href="{{ route('activities.edit', $activity) }}" class="text-primary-600 hover:text-primary-700">编辑</a>
                <form method="POST" action="{{ route('activities.destroy', $activity) }}" class="inline" data-confirm-delete>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700">删除</button>
                </form>
            @endif
        @endauth
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card">
            <h2 class="text-lg font-semibold text-neutral-800 mb-4">活动介绍</h2>
            <div class="prose max-w-none">
                <p class="whitespace-pre-wrap text-neutral-700">{{ $activity->description }}</p>
            </div>
        </div>

        @if($activity->photos->count() > 0)
        <div class="card">
            <h2 class="text-lg font-semibold text-neutral-800 mb-4">活动照片</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($activity->photos as $photo)
                    <div class="aspect-square bg-neutral-100 rounded-lg overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center text-neutral-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($activity->settlement)
        <div class="card">
            <h2 class="text-lg font-semibold text-neutral-800 mb-4">费用结算</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-neutral-100">
                    <span class="text-neutral-600">状态</span>
                    <span class="badge {{ $activity->settlement->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ settlement_status_name($activity->settlement->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-neutral-100">
                    <span class="text-neutral-600">总收入</span>
                    <span class="text-green-600 font-semibold">¥{{ number_format($activity->settlement->total_income, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-neutral-100">
                    <span class="text-neutral-600">总支出</span>
                    <span class="text-red-600 font-semibold">¥{{ number_format($activity->settlement->total_expense, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-neutral-100">
                    <span class="text-neutral-600">结余</span>
                    <span class="font-semibold {{ $activity->settlement->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ¥{{ number_format($activity->settlement->balance, 2) }}
                    </span>
                </div>
                @if($activity->settlement->description)
                <div class="py-2">
                    <span class="text-neutral-600 text-sm">备注说明</span>
                    <p class="text-neutral-700 mt-1">{{ $activity->settlement->description }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <h2 class="text-lg font-semibold text-neutral-800 mb-4">
                报名列表 ({{ $confirmedCount }}/{{ $activity->max_participants }})
            </h2>
            
            @if($confirmedCount > 0)
                <div class="space-y-2">
                    @foreach($activity->confirmedRegistrations as $reg)
                        <div class="flex items-center justify-between py-2 px-3 bg-neutral-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-medium text-sm">
                                    {{ mb_substr($reg->user->username, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-neutral-800">{{ $reg->user->username }}</div>
                                    <div class="text-xs text-neutral-500">报名时间：{{ $reg->registered_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($reg->is_paid)
                                    <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">已付费</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-neutral-500 text-center py-4">暂无报名</p>
            @endif

            @if($waitlistCount > 0)
                <h3 class="text-md font-semibold text-neutral-700 mt-6 mb-3">
                    候补名单 ({{ $waitlistCount }} 人)
                </h3>
                <div class="space-y-2">
                    @foreach($activity->waitlistRegistrations as $reg)
                        <div class="flex items-center justify-between py-2 px-3 bg-orange-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-medium text-sm">
                                    {{ $reg->waitlist_position }}
                                </div>
                                <div>
                                    <div class="font-medium text-neutral-800">{{ $reg->user->username }}</div>
                                    <div class="text-xs text-neutral-500">候补第 {{ $reg->waitlist_position }} 位</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="card">
            <h2 class="text-lg font-semibold text-neutral-800 mb-4">活动信息</h2>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-neutral-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <div class="text-sm text-neutral-500">集合地点</div>
                        <div class="text-neutral-800">{{ $activity->location }}</div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-neutral-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <div class="text-sm text-neutral-500">活动时间</div>
                        <div class="text-neutral-800">{{ $activity->start_time->format('Y-m-d H:i') }}</div>
                        @if($activity->end_time)
                            <div class="text-neutral-500 text-sm">至 {{ $activity->end_time->format('Y-m-d H:i') }}</div>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-neutral-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <div class="text-sm text-neutral-500">活动人数</div>
                        <div class="text-neutral-800">最多 {{ $activity->max_participants }} 人</div>
                        <div class="text-sm text-neutral-500">已报名 {{ $confirmedCount }} 人</div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-neutral-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <div class="text-sm text-neutral-500">活动费用</div>
                        <div class="text-xl font-bold {{ $activity->fee > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            {{ $activity->fee > 0 ? '¥' . number_format($activity->fee, 2) : '免费' }}
                        </div>
                        @if($activity->fee_description)
                            <div class="text-sm text-neutral-500">{{ $activity->fee_description }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @auth
                <div class="mt-6 space-y-3">
                    @if($registration && in_array($registration->status, ['confirmed', 'waitlist', 'attended']))
                        <div class="space-y-2">
                            <div class="text-center py-3 bg-neutral-50 rounded-lg">
                                <span class="badge {{ registration_status_color($registration->status) }}">
                                    {{ registration_status_name($registration->status) }}
                                    @if($registration->status === 'waitlist')
                                        第 {{ $registration->waitlist_position }} 位
                                    @endif
                                </span>
                            </div>
                            @if($registration->status === 'confirmed' && $activity->group)
                                <a href="{{ route('activities.group', $activity) }}" class="btn-primary w-full text-center">
                                    进入活动群聊
                                </a>
                            @endif
                            @if(in_array($registration->status, ['confirmed', 'waitlist']) && $activity->status === 'recruiting')
                                <form method="POST" action="{{ route('activities.cancel-registration', $activity) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary w-full text-red-600 border-red-200 hover:bg-red-50">
                                        取消报名
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif($activity->status === 'recruiting' && $activity->user_id !== auth()->id())
                        <form method="POST" action="{{ route('activities.register', $activity) }}">
                            @csrf
                            <button type="submit" class="btn-primary w-full">
                                {{ $activity->hasAvailableSpots() ? '立即报名' : '加入候补' }}
                            </button>
                        </form>
                        @if(!$activity->hasAvailableSpots())
                            <p class="text-sm text-orange-600 text-center">名额已满，加入候补后如有空位将自动转正</p>
                        @endif
                    @endif
                </div>
            @else
                <div class="mt-6 text-center py-4 bg-neutral-50 rounded-lg">
                    <p class="text-neutral-600 mb-3">请登录后报名活动</p>
                    <a href="{{ route('login') }}" class="btn-primary inline-block">登录报名</a>
                </div>
            @endauth
        </div>

        <div class="card">
            <h2 class="text-lg font-semibold text-neutral-800 mb-4">发起人</h2>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-lg">
                    {{ mb_substr($activity->user->username, 0, 1) }}
                </div>
                <div>
                    <div class="font-medium text-neutral-800">{{ $activity->user->username }}</div>
                    <div class="text-sm text-neutral-500">共发起 {{ $activity->user->activities()->count() }} 个活动</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('activities.index') }}" class="text-primary-600 hover:text-primary-700">← 返回活动列表</a>
</div>
@endsection
