<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_id',
        'name',
        'description',
        'avatar',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function members()
    {
        return $this->hasMany(ActivityGroupMember::class)->whereNull('left_at');
    }

    public function allMembers()
    {
        return $this->hasMany(ActivityGroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(ActivityMessage::class)->orderBy('sent_at', 'desc');
    }

    public function addMember($userId, $role = 'member')
    {
        return $this->members()->updateOrCreate(
            ['user_id' => $userId],
            ['role' => $role, 'left_at' => null]
        );
    }

    public function removeMember($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->update(['left_at' => now()]);
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }
}
