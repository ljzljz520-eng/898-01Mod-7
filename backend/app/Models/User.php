<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function activityRegistrations()
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    public function activityGroups()
    {
        return $this->belongsToMany(ActivityGroup::class, 'activity_group_members')
            ->withPivot('role', 'joined_at', 'left_at')
            ->whereNull('activity_group_members.left_at');
    }

    public function activityPhotos()
    {
        return $this->hasMany(ActivityPhoto::class);
    }

    public function activitySettlements()
    {
        return $this->hasMany(ActivitySettlement::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
