<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'latitude',
        'longitude',
        'start_time',
        'end_time',
        'max_participants',
        'fee',
        'fee_description',
        'status',
        'view_count',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'fee' => 'decimal:2',
        'view_count' => 'integer',
        'max_participants' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    public function confirmedRegistrations()
    {
        return $this->hasMany(ActivityRegistration::class)->where('status', 'confirmed');
    }

    public function waitlistRegistrations()
    {
        return $this->hasMany(ActivityRegistration::class)->where('status', 'waitlist')->orderBy('waitlist_position');
    }

    public function group()
    {
        return $this->hasOne(ActivityGroup::class);
    }

    public function photos()
    {
        return $this->hasMany(ActivityPhoto::class)->orderBy('sort_order');
    }

    public function settlement()
    {
        return $this->hasOne(ActivitySettlement::class);
    }

    public function hasAvailableSpots()
    {
        return $this->confirmedRegistrations()->count() < $this->max_participants;
    }

    public function getWaitlistNextPosition()
    {
        $maxPosition = $this->waitlistRegistrations()->max('waitlist_position');
        return $maxPosition ? $maxPosition + 1 : 1;
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
