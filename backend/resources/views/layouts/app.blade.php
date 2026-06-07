<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '学习交流论坛') - 学习交流论坛</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-50 flex flex-col">
    <nav class="bg-white border-b border-neutral-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('topics.index') }}" class="flex items-center space-x-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded bg-primary-600 text-white text-lg font-bold">
                            学
                        </span>
                        <span class="text-lg font-semibold text-neutral-800">
                            学习交流论坛
                        </span>
                    </a>
                    <div class="hidden md:flex items-center space-x-4 text-sm">
                        <a href="{{ route('topics.index') }}" class="text-neutral-600 hover:text-primary-600">
                            讨论
                        </a>
                        <a href="{{ route('activities.index') }}" class="text-neutral-600 hover:text-primary-600">
                            同城活动
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    @auth
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('topics.create') }}" class="btn-primary">
                                发布主题
                            </a>
                            <div class="relative" id="user-menu">
                                <button id="user-menu-button" class="flex items-center gap-1 text-neutral-600 hover:text-primary-600">
                                    <span>{{ auth()->user()->username }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-100 py-1 z-50">
                                    <a href="{{ route('activities.my') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                        我发起的活动
                                    </a>
                                    <a href="{{ route('activities.joined') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                        我参与的活动
                                    </a>
                                    <div class="border-t border-neutral-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-50">
                                            登出
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-neutral-500 hover:text-neutral-800">登录</a>
                        <a href="{{ route('register') }}" class="btn-primary">注册</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full">
        <div class="min-w-0">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative transition-opacity duration-200" role="alert" data-auto-dismiss="3000">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-opacity duration-200" role="alert" data-auto-dismiss="3000">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-opacity duration-200" role="alert" data-auto-dismiss="3000">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-xs text-neutral-500">© 2024 学习交流论坛 · Inspired by SegmentFault UI</p>
        </div>
    </footer>
</body>
</html>
