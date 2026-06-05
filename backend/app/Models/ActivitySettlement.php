<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivitySettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_id',
        'user_id',
        'total_income',
        'total_expense',
        'balance',
        'description',
        'expense_details',
        'income_details',
        'status',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'balance' => 'decimal:2',
        'expense_details' => 'array',
        'income_details' => 'array',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculateBalance()
    {
        $this->balance = $this->total_income - $this->total_expense;
        return $this;
    }

    public function submit()
    {
        $this->calculateBalance();
        $this->status = 'submitted';
        $this->save();
        return $this;
    }

    public function approve()
    {
        $this->status = 'approved';
        $this->save();
        return $this;
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
        return $this;
    }
}
