<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_group_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(ActivityGroup::class, 'activity_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['owner', 'admin']);
    }
}
