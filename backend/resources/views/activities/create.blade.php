@extends('layouts.app')

@section('title', '发布活动')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-neutral-800 mb-6">发布活动</h1>
    
    <div class="card">
        <form method="POST" action="{{ route('activities.store') }}">
            @csrf
            
            <div class="mb-4">
                <label for="title" class="block text-neutral-700 text-sm font-medium mb-2">活动标题 <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="input-field @error('title') border-red-500 @enderror"
                       placeholder="请输入活动标题" autofocus>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="category" class="block text-neutral-700 text-sm font-medium mb-2">活动分类 <span class="text-red-500">*</span></label>
                    <select id="category" name="category" required
                            class="input-field @error('category') border-red-500 @enderror">
                        <option value="">请选择分类</option>
                        <option value="badminton" {{ old('category') == 'badminton' ? 'selected' : '' }}>🏸 羽毛球</option>
                        <option value="book_club" {{ old('category') == 'book_club' ? 'selected' : '' }}>📚 读书会</option>
                        <option value="parent_child_market" {{ old('category') == 'parent_child_market' ? 'selected' : '' }}>👨‍👩‍👧 亲子市集</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>📅 其他活动</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_participants" class="block text-neutral-700 text-sm font-medium mb-2">人数上限 <span class="text-red-500">*</span></label>
                    <input type="number" id="max_participants" name="max_participants" value="{{ old('max_participants', 10) }}" required min="1"
                           class="input-field @error('max_participants') border-red-500 @enderror"
                           placeholder="最多参与人数">
                    @error('max_participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="location" class="block text-neutral-700 text-sm font-medium mb-2">集合地点 <span class="text-red-500">*</span></label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" required
                       class="input-field @error('location') border-red-500 @enderror"
                       placeholder="请输入详细的集合地址">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_time" class="block text-neutral-700 text-sm font-medium mb-2">开始时间 <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                           class="input-field @error('start_time') border-red-500 @enderror">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-neutral-700 text-sm font-medium mb-2">结束时间</label>
                    <input type="datetime-local" id="end_time" name="end_time" value="{{ old('end_time') }}"
                           class="input-field @error('end_time') border-red-500 @enderror">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="fee" class="block text-neutral-700 text-sm font-medium mb-2">活动费用 (元)</label>
                    <input type="number" id="fee" name="fee" value="{{ old('fee', 0) }}" min="0" step="0.01"
                           class="input-field @error('fee') border-red-500 @enderror"
                           placeholder="0 表示免费">
                    @error('fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fee_description" class="block text-neutral-700 text-sm font-medium mb-2">费用说明</label>
                    <input type="text" id="fee_description" name="fee_description" value="{{ old('fee_description') }}"
                           class="input-field @error('fee_description') border-red-500 @enderror"
                           placeholder="费用包含哪些内容">
                    @error('fee_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-neutral-700 text-sm font-medium mb-2">活动详情 <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="8" required
                          class="input-field @error('description') border-red-500 @enderror"
                          placeholder="请详细描述活动内容、注意事项等...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn-primary">发布活动</button>
                <button type="submit" name="status" value="draft" class="btn-secondary">保存草稿</button>
                <a href="{{ route('activities.index') }}" class="btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
