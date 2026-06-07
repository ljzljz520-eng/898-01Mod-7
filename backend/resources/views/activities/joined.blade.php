@extends('layouts.app')

@section('title', '我参与的活动')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-neutral-800">我参与的活动</h1>
    <p class="text-sm text-neutral-500 mt-1">查看你报名参加的所有活动</p>
</div>

<div class="space-y-3">
    @forelse($registrations as $registration)
        <div class="card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge {{ activity_category_color($registration->activity->category) }} text-[11px]">
                            {{ activity_category_name($registration->activity->category) }}
                        </span>
                        <span class="badge {{ activity_status_color($registration->activity->status) }} text-[11px]">
                            {{ activity_status_name($registration->activity->status) }}
                        </span>
                        <span class="badge {{ registration_status_color($registration->status) }} text-[11px]">
                            {{ registration_status_name($registration->status) }}
                            @if($registration->status === 'waitlist')
                                第 {{ $registration->waitlist_position }} 位
                            @endif
                        </span>
                    </div>
                    <a href="{{ route('activities.show', $registration->activity) }}" class="text-lg font-semibold text-neutral-800 hover:text-primary-600">
                        {{ $registration->activity->title }}
                    </a>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-neutral-500">
                        <span>发起人：{{ $registration->activity->user->username }}</span>
                        <span>📍 {{ $registration->activity->location }}</span>
                        <span>⏰ {{ $registration->activity->start_time->format('Y-m-d H:i') }}</span>
                        <span>👥 {{ $registration->activity->confirmedRegistrations->count() }}/{{ $registration->activity->max_participants }} 人</span>
                        <span class="{{ $registration->activity->fee > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            {{ $registration->activity->fee > 0 ? '¥' . $registration->activity->fee : '免费' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($registration->status === 'confirmed' && $registration->activity->group)
                        <a href="{{ route('activities.group', $registration->activity) }}" class="btn-primary text-sm">
                            进入群聊
                        </a>
                    @endif
                    <a href="{{ route('activities.show', $registration->activity) }}" class="btn-secondary text-sm">
                        查看详情
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center py-12">
            <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-neutral-500 text-lg mb-2">你还没有参与任何活动</p>
            <a href="{{ route('activities.index') }}" class="btn-primary inline-block">去发现活动</a>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $registrations->links('pagination.custom') }}
</div>
@endsection
