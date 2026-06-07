<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Activity::with('user')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc');

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(12)->appends(request()->query());

        $categories = [
            'all' => '全部分类',
            'badminton' => '羽毛球',
            'book_club' => '读书会',
            'parent_child_market' => '亲子市集',
            'other' => '其他',
        ];

        $statuses = [
            'all' => '全部状态',
            'recruiting' => '招募中',
            'in_progress' => '进行中',
            'completed' => '已结束',
        ];

        return view('activities.index', compact('activities', 'categories', 'statuses'));
    }

    public function show(Activity $activity)
    {
        if ($activity->status === 'draft' && $activity->user_id !== auth()->id()) {
            abort(404);
        }

        $activity->incrementViewCount();
        $activity->load([
            'user',
            'confirmedRegistrations.user',
            'waitlistRegistrations.user',
            'group',
            'photos',
            'settlement',
        ]);

        $registration = null;
        if (auth()->check()) {
            $registration = $activity->registrations()
                ->where('user_id', auth()->id())
                ->first();
        }

        $confirmedCount = $activity->confirmedRegistrations()->count();
        $waitlistCount = $activity->waitlistRegistrations()->count();

        return view('activities.show', compact(
            'activity',
            'registration',
            'confirmedCount',
            'waitlistCount'
        ));
    }

    public function create()
    {
        return view('activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => ['required', Rule::in(['badminton', 'book_club', 'parent_child_market', 'other'])],
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'max_participants' => 'required|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'fee_description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['draft', 'recruiting'])],
        ]);

        $activity = Activity::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'max_participants' => $validated['max_participants'],
            'fee' => $validated['fee'] ?? 0,
            'fee_description' => $validated['fee_description'] ?? null,
            'status' => $validated['status'] ?? 'recruiting',
        ]);

        if ($activity->status !== 'draft') {
            $group = ActivityGroup::create([
                'activity_id' => $activity->id,
                'name' => $activity->title . ' 活动群',
                'description' => $activity->description,
            ]);

            $group->addMember(auth()->id(), 'owner');
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', $activity->status === 'draft' ? '活动草稿已保存' : '活动发布成功');
    }

    public function edit(Activity $activity)
    {
        if ($activity->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        return view('activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => ['sometimes', Rule::in(['badminton', 'book_club', 'parent_child_market', 'other'])],
            'location' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_time' => 'sometimes|date',
            'end_time' => 'nullable|date|after:start_time',
            'max_participants' => 'sometimes|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'fee_description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['draft', 'recruiting', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $wasDraft = $activity->status === 'draft';
        $activity->update($validated);

        if ($wasDraft && $activity->status !== 'draft' && !$activity->group) {
            $group = ActivityGroup::create([
                'activity_id' => $activity->id,
                'name' => $activity->title . ' 活动群',
                'description' => $activity->description,
            ]);

            $group->addMember(auth()->id(), 'owner');
        }

        if ($activity->group && isset($validated['title'])) {
            $activity->group->update(['name' => $validated['title'] . ' 活动群']);
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', '活动更新成功');
    }

    public function destroy(Activity $activity)
    {
        if ($activity->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', '活动删除成功');
    }

    public function register(Request $request, Activity $activity)
    {
        if ($activity->user_id === auth()->id()) {
            return redirect()->back()->with('error', '发起人不能报名自己的活动');
        }

        if ($activity->status !== 'recruiting') {
            return redirect()->back()->with('error', '活动不在招募中');
        }

        $existing = $activity->registrations()
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            if ($existing->status === 'cancelled') {
                $existing->restore();
            } else {
                return redirect()->back()->with('error', '已报名该活动');
            }
        }

        $hasSpots = $activity->hasAvailableSpots();
        $status = $hasSpots ? 'confirmed' : 'waitlist';
        $waitlistPosition = $hasSpots ? null : $activity->getWaitlistNextPosition();

        $registration = $activity->registrations()->create([
            'user_id' => auth()->id(),
            'status' => $status,
            'waitlist_position' => $waitlistPosition,
            'note' => $request->note,
        ]);

        if ($status === 'confirmed' && $activity->group) {
            $activity->group->addMember(auth()->id(), 'member');

            $activity->group->messages()->create([
                'user_id' => auth()->id(),
                'type' => 'system',
                'content' => auth()->user()->username . ' 加入了活动群',
            ]);
        }

        $message = $status === 'confirmed'
            ? '报名成功，已加入活动群'
            : '报名成功，当前为候补第 ' . $waitlistPosition . ' 位';

        return redirect()->back()->with('success', $message);
    }

    public function cancelRegistration(Activity $activity)
    {
        $registration = $activity->registrations()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['confirmed', 'waitlist'])
            ->first();

        if (!$registration) {
            return redirect()->back()->with('error', '未找到报名记录');
        }

        $wasConfirmed = $registration->status === 'confirmed';
        $registration->cancel();

        if ($wasConfirmed && $activity->group) {
            $activity->group->removeMember(auth()->id());

            $activity->group->messages()->create([
                'user_id' => auth()->id(),
                'type' => 'system',
                'content' => auth()->user()->username . ' 退出了活动群',
            ]);

            $nextWaitlist = $activity->waitlistRegistrations()->first();
            if ($nextWaitlist) {
                $nextWaitlist->confirm();

                if ($activity->group) {
                    $activity->group->addMember($nextWaitlist->user_id, 'member');

                    $activity->group->messages()->create([
                        'user_id' => $nextWaitlist->user_id,
                        'type' => 'system',
                        'content' => $nextWaitlist->user->username . ' 从候补名单转正，加入活动群',
                    ]);
                }

                $waitlistRegistrations = $activity->waitlistRegistrations()->get();
                $newPosition = 1;
                foreach ($waitlistRegistrations as $reg) {
                    $reg->waitlist_position = $newPosition++;
                    $reg->save();
                }
            }
        } elseif ($registration->status === 'waitlist') {
            $waitlistRegistrations = $activity->waitlistRegistrations()->get();
            $newPosition = 1;
            foreach ($waitlistRegistrations as $reg) {
                if ($reg->id === $registration->id) continue;
                $reg->waitlist_position = $newPosition++;
                $reg->save();
            }
        }

        return redirect()->back()->with('success', '已取消报名');
    }

    public function myActivities(Request $request)
    {
        $activities = Activity::where('user_id', auth()->id())
            ->with('confirmedRegistrations')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('activities.my', compact('activities'));
    }

    public function myJoined(Request $request)
    {
        $registrations = auth()->user()->activityRegistrations()
            ->with('activity.user', 'activity.confirmedRegistrations')
            ->whereIn('status', ['confirmed', 'waitlist', 'attended'])
            ->orderBy('registered_at', 'desc')
            ->paginate(10);

        return view('activities.joined', compact('registrations'));
    }

    public function group(Request $request, Activity $activity)
    {
        $group = $activity->group;
        if (!$group) {
            abort(404, '活动群不存在');
        }

        if (!$group->isMember(auth()->id()) && $activity->user_id !== auth()->id()) {
            abort(403, '无权限访问该群聊');
        }

        $group->load(['members.user', 'messages.user']);

        $messages = $group->messages()
            ->with('user')
            ->orderBy('sent_at', 'desc')
            ->paginate(50);

        return view('activities.group', compact('activity', 'group', 'messages'));
    }

    public function sendMessage(Request $request, Activity $activity)
    {
        $group = $activity->group;
        if (!$group) {
            return redirect()->back()->with('error', '活动群不存在');
        }

        if (!$group->isMember(auth()->id())) {
            return redirect()->back()->with('error', '不是群成员');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $group->messages()->create([
            'user_id' => auth()->id(),
            'type' => 'text',
            'content' => $request->content,
        ]);

        return redirect()->back();
    }
}
