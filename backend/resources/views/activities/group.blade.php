@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('activities.show', $activity) }}" class="text-primary-600 hover:text-primary-700">← 返回活动详情</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="card h-[600px] flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
                <div>
                    <h2 class="text-xl font-semibold text-neutral-800">{{ $group->name }}</h2>
                    <p class="text-sm text-neutral-500">{{ $group->members->count() }} 位成员</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-sm text-green-600">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        {{ $activity->title }}
                    </span>
                </div>
            </div>

            <div id="messages" class="flex-1 overflow-y-auto py-4 space-y-4" style="scroll-behavior: smooth;">
                @forelse($messages as $message)
                    @if($message->type === 'system')
                        <div class="text-center">
                            <span class="inline-block text-xs text-neutral-500 bg-neutral-100 px-3 py-1 rounded-full">
                                {{ $message->content }}
                            </span>
                        </div>
                    @else
                        <div class="flex gap-3 {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                            <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-white font-medium
                                {{ $message->user_id === auth()->id() ? 'bg-primary-500' : 'bg-neutral-300' }}">
                                {{ mb_substr($message->user->username, 0, 1) }}
                            </div>
                            <div class="max-w-[70%]">
                                <div class="flex items-center gap-2 mb-1 {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                    <span class="text-sm font-medium text-neutral-700">{{ $message->user->username }}</span>
                                    <span class="text-xs text-neutral-400">{{ $message->sent_at->format('H:i') }}</span>
                                </div>
                                <div class="px-4 py-2 rounded-2xl {{ $message->user_id === auth()->id() 
                                    ? 'bg-primary-500 text-white rounded-tr-none' 
                                    : 'bg-neutral-100 text-neutral-800 rounded-tl-none' }}">
                                    {{ $message->content }}
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12 text-neutral-400">
                        <p>还没有消息，快来发送第一条消息吧！</p>
                    </div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('activities.send-message', $activity) }}" class="pt-4 border-t border-neutral-100">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="content" required maxlength="1000"
                           class="flex-1 input-field"
                           placeholder="输入消息...">
                    <button type="submit" class="btn-primary px-6">发送</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card">
            <h3 class="text-lg font-semibold text-neutral-800 mb-4">群成员</h3>
            <div class="space-y-2 max-h-[400px] overflow-y-auto">
                @foreach($group->members as $member)
                    <div class="flex items-center gap-3 py-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium
                            {{ $member->role === 'owner' ? 'bg-amber-500' : ($member->role === 'admin' ? 'bg-purple-500' : 'bg-neutral-300') }}">
                            {{ mb_substr($member->user->username, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-neutral-800 truncate">{{ $member->user->username }}</span>
                                @if($member->role === 'owner')
                                    <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded">群主</span>
                                @elseif($member->role === 'admin')
                                    <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded">管理员</span>
                                @endif
                            </div>
                            <div class="text-xs text-neutral-400">{{ $member->joined_at->format('Y-m-d') }} 加入</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h3 class="text-lg font-semibold text-neutral-800 mb-4">活动信息</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-neutral-500">活动名称</span>
                    <p class="font-medium text-neutral-800 mt-1">{{ $activity->title }}</p>
                </div>
                <div>
                    <span class="text-neutral-500">活动状态</span>
                    <p class="mt-1">
                        <span class="badge {{ activity_status_color($activity->status) }}">
                            {{ activity_status_name($activity->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-neutral-500">集合地点</span>
                    <p class="text-neutral-800 mt-1">{{ $activity->location }}</p>
                </div>
                <div>
                    <span class="text-neutral-500">活动时间</span>
                    <p class="text-neutral-800 mt-1">{{ $activity->start_time->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('activities.show', $activity) }}" class="mt-4 btn-secondary w-full text-center block">
                查看活动详情
            </a>
        </div>
    </div>
</div>
@endsection
