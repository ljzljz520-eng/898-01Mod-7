<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_id',
        'user_id',
        'status',
        'waitlist_position',
        'note',
        'paid_amount',
        'is_paid',
        'registered_at',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'registered_at' => 'datetime',
        'waitlist_position' => 'integer',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeWaitlist($query)
    {
        return $query->where('status', 'waitlist')->orderBy('waitlist_position');
    }

    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'waitlist_position' => null,
        ]);
    }

    public function moveToWaitlist($position = null)
    {
        $this->update([
            'status' => 'waitlist',
            'waitlist_position' => $position,
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'waitlist_position' => null,
        ]);
    }
}
