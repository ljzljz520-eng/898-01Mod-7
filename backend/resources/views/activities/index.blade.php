@extends('layouts.app')

@section('title', '同城活动')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-neutral-800">同城活动</h1>
    <p class="text-sm text-neutral-500 mt-1">发现身边的精彩活动，认识志同道合的邻居</p>
</div>

<div class="mb-4 flex flex-col sm:flex-row gap-3">
    <form method="GET" action="{{ route('activities.index') }}" class="flex-1 flex flex-wrap gap-2 items-center">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="搜索活动名称、地点..." 
               class="flex-1 min-w-[200px] input-field">
        <select name="category" class="input-field w-auto text-sm">
            @foreach($categories as $key => $name)
                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <select name="status" class="input-field w-auto text-sm">
            @foreach($statuses as $key => $name)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary text-sm px-3">搜索</button>
    </form>
    @auth
        <a href="{{ route('activities.create') }}" class="btn-primary text-center whitespace-nowrap">
            + 发布活动
        </a>
    @endauth
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($activities as $activity)
        <div class="card hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="badge {{ activity_category_color($activity->category) }} text-[11px]">
                        {{ activity_category_name($activity->category) }}
                    </span>
                    <span class="badge {{ activity_status_color($activity->status) }} text-[11px]">
                        {{ activity_status_name($activity->status) }}
                    </span>
                </div>
            </div>
            
            <a href="{{ route('activities.show', $activity) }}" class="block">
                <h3 class="text-lg font-semibold text-neutral-800 hover:text-primary-600 mb-2 line-clamp-2">
                    {{ $activity->title }}
                </h3>
            </a>
            
            <p class="text-neutral-600 text-sm mb-3 line-clamp-2">
                {{ Str::limit($activity->description, 100) }}
            </p>
            
            <div class="space-y-2 text-sm text-neutral-500">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">{{ $activity->location }}</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $activity->start_time->format('Y-m-d H:i') }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>{{ $activity->confirmedRegistrations->count() }}/{{ $activity->max_participants }} 人</span>
                    </div>
                    <div class="font-semibold {{ $activity->fee > 0 ? 'text-orange-600' : 'text-green-600' }}">
                        {{ $activity->fee > 0 ? '¥' . $activity->fee : '免费' }}
                    </div>
                </div>
            </div>
            
            <div class="mt-3 pt-3 border-t border-neutral-100 flex items-center justify-between text-xs text-neutral-500">
                <span>发起人：{{ $activity->user->username }}</span>
                <span>{{ $activity->view_count }} 浏览</span>
            </div>
        </div>
    @empty
        <div class="col-span-full card text-center py-12">
            <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-neutral-500 text-lg mb-2">暂无活动</p>
            <p class="text-neutral-400 text-sm">快来发布第一个同城活动吧！</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $activities->links('pagination.custom') }}
</div>
@endsection
