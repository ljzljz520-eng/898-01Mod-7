<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivityMessage;
use App\Models\ActivityPhoto;
use App\Models\ActivityRegistration;
use App\Models\ActivitySettlement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('username', ['user1', 'user2', 'user3', 'user4', 'user5', 'user6', 'user7', 'user8'])->get();
        if ($users->count() < 8) {
            return;
        }

        $activitiesData = [
            [
                'title' => '周末羽毛球双打活动',
                'description' => '本周六下午羽毛球双打活动，欢迎喜欢羽毛球的朋友报名参加！场地已预订，球拍自备，也可以现场租用。水平不限，重在参与和交流。',
                'category' => 'badminton',
                'location' => '朝阳区羽毛球馆（朝阳路88号）',
                'latitude' => 39.92,
                'longitude' => 116.46,
                'start_time' => Carbon::now()->addDays(3)->setHour(14)->setMinute(0),
                'end_time' => Carbon::now()->addDays(3)->setHour(17)->setMinute(0),
                'max_participants' => 8,
                'fee' => 50.00,
                'fee_description' => '场地费AA，包含羽毛球，球拍自备',
                'status' => 'recruiting',
                'user' => $users[0],
            ],
            [
                'title' => '《百年孤独》读书会',
                'description' => '本周日下午一起共读马尔克斯的《百年孤独》，分享各自的阅读感受。每人准备一段自己最喜欢的片段朗读，然后自由交流讨论。咖啡馆消费自理。',
                'category' => 'book_club',
                'location' => '海淀区漫咖啡（中关村大街15号）',
                'latitude' => 39.98,
                'longitude' => 116.31,
                'start_time' => Carbon::now()->addDays(4)->setHour(15)->setMinute(0),
                'end_time' => Carbon::now()->addDays(4)->setHour(18)->setMinute(0),
                'max_participants' => 12,
                'fee' => 0.00,
                'fee_description' => '免费参加，店内消费自理',
                'status' => 'recruiting',
                'user' => $users[1],
            ],
            [
                'title' => '亲子创意市集',
                'description' => '六一儿童节特别活动！欢迎家长带小朋友参加，孩子们可以自己摆摊出售闲置玩具、书籍、手工作品等。培养孩子的社交能力和财商。现场还有亲子游戏互动。',
                'category' => 'parent_child_market',
                'location' => '丰台区青少年活动中心广场',
                'latitude' => 39.85,
                'longitude' => 116.28,
                'start_time' => Carbon::now()->addDays(10)->setHour(9)->setMinute(0),
                'end_time' => Carbon::now()->addDays(10)->setHour(16)->setMinute(0),
                'max_participants' => 30,
                'fee' => 20.00,
                'fee_description' => '摊位费20元，用于活动物料',
                'status' => 'recruiting',
                'user' => $users[2],
            ],
            [
                'title' => '羽毛球新手入门教学',
                'description' => '专门为羽毛球新手准备的教学活动，有专业教练指导基础动作和规则。球拍可免费提供，欢迎零基础的朋友报名！',
                'category' => 'badminton',
                'location' => '东城区体育馆（东四北大街12号）',
                'latitude' => 39.93,
                'longitude' => 116.42,
                'start_time' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0),
                'end_time' => Carbon::now()->addDays(7)->setHour(12)->setMinute(0),
                'max_participants' => 6,
                'fee' => 80.00,
                'fee_description' => '包含教练费和场地费，球拍免费提供',
                'status' => 'recruiting',
                'user' => $users[0],
            ],
            [
                'title' => '科幻小说主题读书会',
                'description' => '本月主题：刘慈欣科幻作品。可以读《三体》《流浪地球》《球状闪电》等，一起探讨科幻文学中的科学精神和人文关怀。',
                'category' => 'book_club',
                'location' => '西城区Page One书店（北京坊店）',
                'latitude' => 39.90,
                'longitude' => 116.39,
                'start_time' => Carbon::now()->addDays(14)->setHour(14)->setMinute(0),
                'end_time' => Carbon::now()->addDays(14)->setHour(17)->setMinute(0),
                'max_participants' => 15,
                'fee' => 0.00,
                'fee_description' => '免费参加',
                'status' => 'recruiting',
                'user' => $users[3],
            ],
            [
                'title' => '亲子户外徒步活动',
                'description' => '带孩子走进大自然，一起徒步香山，认识植物和昆虫。全程约5公里，难度适中，适合6-12岁儿童。请准备好舒适的运动鞋和足够的水。',
                'category' => 'parent_child_market',
                'location' => '香山公园东门集合',
                'latitude' => 39.99,
                'longitude' => 116.18,
                'start_time' => Carbon::now()->addDays(6)->setHour(8)->setMinute(30),
                'end_time' => Carbon::now()->addDays(6)->setHour(15)->setMinute(0),
                'max_participants' => 20,
                'fee' => 0.00,
                'fee_description' => '免费，门票自理',
                'status' => 'in_progress',
                'user' => $users[4],
            ],
            [
                'title' => '羽毛球友谊赛',
                'description' => '社区羽毛球友谊赛，分为单打和双打两个项目，欢迎有一定基础的居民报名参加。比赛采用小组循环赛制，获胜者有小奖品！',
                'category' => 'badminton',
                'location' => '石景山体育馆',
                'latitude' => 39.91,
                'longitude' => 116.22,
                'start_time' => Carbon::now()->addDays(2)->setHour(9)->setMinute(0),
                'end_time' => Carbon::now()->addDays(2)->setHour(18)->setMinute(0),
                'max_participants' => 16,
                'fee' => 100.00,
                'fee_description' => '报名费100元，包含比赛用球和奖品',
                'status' => 'recruiting',
                'user' => $users[5],
            ],
            [
                'title' => '亲子烘焙体验课',
                'description' => '和孩子一起动手做饼干和小蛋糕！专业烘焙老师指导，所有材料都已准备好，成品可以带回家。适合4-10岁儿童及家长。',
                'category' => 'parent_child_market',
                'location' => '通州区亲子烘焙工坊（新华大街200号）',
                'latitude' => 39.91,
                'longitude' => 116.66,
                'start_time' => Carbon::now()->addDays(12)->setHour(10)->setMinute(0),
                'end_time' => Carbon::now()->addDays(12)->setHour(12)->setMinute(30),
                'max_participants' => 10,
                'fee' => 128.00,
                'fee_description' => '一大一小128元，包含所有材料',
                'status' => 'recruiting',
                'user' => $users[6],
            ],
            [
                'title' => '历史类书籍读书分享会',
                'description' => '分享你最喜欢的历史书籍，可以是《万历十五年》《全球通史》《史记》等。以书会友，畅谈历史。',
                'category' => 'book_club',
                'location' => '国家图书馆古籍馆',
                'latitude' => 39.95,
                'longitude' => 116.32,
                'start_time' => Carbon::now()->addDays(20)->setHour(14)->setMinute(0),
                'end_time' => Carbon::now()->addDays(20)->setHour(17)->setMinute(0),
                'max_participants' => 20,
                'fee' => 0.00,
                'fee_description' => '免费',
                'status' => 'draft',
                'user' => $users[1],
            ],
            [
                'title' => '上周羽毛球活动回顾',
                'description' => '感谢大家参加上周的羽毛球活动！大家打得都很尽兴，后续还会组织更多活动，敬请期待。',
                'category' => 'badminton',
                'location' => '朝阳区羽毛球馆',
                'latitude' => 39.92,
                'longitude' => 116.46,
                'start_time' => Carbon::now()->subDays(3)->setHour(14)->setMinute(0),
                'end_time' => Carbon::now()->subDays(3)->setHour(17)->setMinute(0),
                'max_participants' => 8,
                'fee' => 50.00,
                'fee_description' => '场地费AA',
                'status' => 'completed',
                'user' => $users[0],
            ],
        ];

        $activityMessages = [
            '大家好，很高兴加入这个活动群！',
            '请问场地有更衣室吗？',
            '我是新手，大家多多关照~',
            '当天天气怎么样？需要带水吗？',
            '开车去的话，停车方便吗？',
            '我可以带朋友一起来吗？',
            '期待和大家见面！',
            '大家可以建个微信群方便联系',
            '请问附近有地铁站吗？',
            '第一次参加这种活动，有点小激动！',
        ];

        foreach ($activitiesData as $index => $activityData) {
            $activityUser = $activityData['user'];
            unset($activityData['user']);

            $activity = Activity::firstOrCreate(
                ['title' => $activityData['title']],
                array_merge($activityData, [
                    'user_id' => $activityUser->id,
                    'view_count' => rand(20, 200),
                    'created_at' => Carbon::now()->subDays(rand(1, 15)),
                ])
            );

            if ($activity->wasRecentlyCreated) {
                $group = ActivityGroup::create([
                    'activity_id' => $activity->id,
                    'name' => $activity->title . ' 活动群',
                    'description' => $activity->description,
                    'is_active' => true,
                ]);

                $group->addMember($activityUser->id, 'owner');

                if ($activity->status !== 'draft') {
                    $otherUsers = $users->filter(fn($u) => $u->id !== $activityUser->id);
                    $numParticipants = min(
                        rand(2, $activity->max_participants),
                        $activity->max_participants,
                        $otherUsers->count()
                    );

                    $selectedUsers = $otherUsers->random($numParticipants);

                    $confirmedCount = 0;
                    foreach ($selectedUsers as $idx => $participant) {
                        if ($confirmedCount < $activity->max_participants) {
                            $status = 'confirmed';
                            $waitlistPosition = null;
                            $confirmedCount++;

                            $group->addMember($participant->id, 'member');

                            if ($index < 5 && $idx < 3) {
                                ActivityMessage::create([
                                    'activity_group_id' => $group->id,
                                    'user_id' => $participant->id,
                                    'type' => 'text',
                                    'content' => $activityMessages[array_rand($activityMessages)],
                                    'sent_at' => Carbon::now()->subDays(rand(1, 5)),
                                ]);
                            }
                        } else {
                            $status = 'waitlist';
                            $waitlistPosition = $confirmedCount - $activity->max_participants + 1;
                        }

                        ActivityRegistration::create([
                            'activity_id' => $activity->id,
                            'user_id' => $participant->id,
                            'status' => $status,
                            'waitlist_position' => $waitlistPosition,
                            'is_paid' => $activity->fee > 0 ? (bool)rand(0, 1) : true,
                            'paid_amount' => $activity->fee > 0 ? (rand(0, 1) ? $activity->fee : 0) : 0,
                            'registered_at' => Carbon::now()->subDays(rand(1, 10)),
                        ]);
                    }
                }

                if ($activity->status === 'completed') {
                    for ($i = 1; $i <= 3; $i++) {
                        ActivityPhoto::create([
                            'activity_id' => $activity->id,
                            'user_id' => $activityUser->id,
                            'image_path' => 'activity_photos/' . $activity->id . '/photo_' . $i . '.jpg',
                            'thumbnail_path' => 'activity_photos/' . $activity->id . '/thumb_' . $i . '.jpg',
                            'caption' => '活动现场照片 ' . $i,
                            'sort_order' => $i,
                        ]);
                    }

                    ActivitySettlement::create([
                        'activity_id' => $activity->id,
                        'user_id' => $activityUser->id,
                        'total_income' => $activity->confirmedRegistrations()->count() * $activity->fee,
                        'total_expense' => 200.00,
                        'balance' => ($activity->confirmedRegistrations()->count() * $activity->fee) - 200.00,
                        'description' => '场地费200元，球费已包含在报名费中',
                        'expense_details' => json_encode([
                            ['item' => '场地费2小时', 'amount' => 160.00],
                            ['item' => '羽毛球1筒', 'amount' => 40.00],
                        ]),
                        'income_details' => json_encode([
                            ['item' => '报名费', 'amount' => $activity->confirmedRegistrations()->count() * $activity->fee],
                        ]),
                        'status' => 'approved',
                    ]);
                }
            }
        }
    }
}
