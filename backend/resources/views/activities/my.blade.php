@extends('layouts.app')

@section('title', '我发起的活动')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-800">我发起的活动</h1>
        <p class="text-sm text-neutral-500 mt-1">管理你发起的所有活动</p>
    </div>
    <a href="{{ route('activities.create') }}" class="btn-primary text-center whitespace-nowrap">
        + 发布新活动
    </a>
</div>

<div class="space-y-3">
    @forelse($activities as $activity)
        <div class="card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge {{ activity_category_color($activity->category) }} text-[11px]">
                            {{ activity_category_name($activity->category) }}
                        </span>
                        <span class="badge {{ activity_status_color($activity->status) }} text-[11px]">
                            {{ activity_status_name($activity->status) }}
                        </span>
                    </div>
                    <a href="{{ route('activities.show', $activity) }}" class="text-lg font-semibold text-neutral-800 hover:text-primary-600">
                        {{ $activity->title }}
                    </a>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-neutral-500">
                        <span>📍 {{ $activity->location }}</span>
                        <span>⏰ {{ $activity->start_time->format('Y-m-d H:i') }}</span>
                        <span>👥 {{ $activity->confirmedRegistrations->count() }}/{{ $activity->max_participants }} 人</span>
                        <span class="{{ $activity->fee > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            {{ $activity->fee > 0 ? '¥' . $activity->fee : '免费' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($activity->group)
                        <a href="{{ route('activities.group', $activity) }}" class="btn-secondary text-sm">
                            群聊
                        </a>
                    @endif
                    <a href="{{ route('activities.edit', $activity) }}" class="btn-secondary text-sm">
                        编辑
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center py-12">
            <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-neutral-500 text-lg mb-2">你还没有发起活动</p>
            <a href="{{ route('activities.create') }}" class="btn-primary inline-block">发起第一个活动</a>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $activities->links('pagination.custom') }}
</div>
@endsection
