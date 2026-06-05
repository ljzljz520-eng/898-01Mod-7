<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_group_id',
        'user_id',
        'type',
        'content',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(ActivityGroup::class, 'activity_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }
}
