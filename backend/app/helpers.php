<?php

if (!function_exists('category_name')) {
    /**
     * 获取分类的中文名称
     */
    function category_name(string $category): string
    {
        return match($category) {
            'general' => '综合讨论',
            'tech' => '技术交流',
            'study' => '学习心得',
            'question' => '问题求助',
            default => $category,
        };
    }
}

if (!function_exists('activity_category_name')) {
    function activity_category_name(string $category): string
    {
        return match($category) {
            'badminton' => '羽毛球',
            'book_club' => '读书会',
            'parent_child_market' => '亲子市集',
            'other' => '其他活动',
            default => $category,
        };
    }
}

if (!function_exists('activity_category_color')) {
    function activity_category_color(string $category): string
    {
        return match($category) {
            'badminton' => 'bg-green-100 text-green-700',
            'book_club' => 'bg-blue-100 text-blue-700',
            'parent_child_market' => 'bg-pink-100 text-pink-700',
            'other' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('activity_status_name')) {
    function activity_status_name(string $status): string
    {
        return match($status) {
            'draft' => '草稿',
            'recruiting' => '招募中',
            'in_progress' => '进行中',
            'completed' => '已结束',
            'cancelled' => '已取消',
            default => $status,
        };
    }
}

if (!function_exists('activity_status_color')) {
    function activity_status_color(string $status): string
    {
        return match($status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'recruiting' => 'bg-emerald-100 text-emerald-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            'completed' => 'bg-blue-100 text-blue-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('registration_status_name')) {
    function registration_status_name(string $status): string
    {
        return match($status) {
            'pending' => '待确认',
            'confirmed' => '已报名',
            'waitlist' => '候补',
            'cancelled' => '已取消',
            'attended' => '已参加',
            default => $status,
        };
    }
}

if (!function_exists('registration_status_color')) {
    function registration_status_color(string $status): string
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'confirmed' => 'bg-green-100 text-green-700',
            'waitlist' => 'bg-orange-100 text-orange-700',
            'cancelled' => 'bg-red-100 text-red-700',
            'attended' => 'bg-blue-100 text-blue-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('settlement_status_name')) {
    function settlement_status_name(string $status): string
    {
        return match($status) {
            'draft' => '草稿',
            'submitted' => '待审核',
            'approved' => '已通过',
            'rejected' => '已驳回',
            default => $status,
        };
    }
}
