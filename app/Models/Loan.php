<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
    'group_id',
    'user_id',
    'amount',
    'total_payable',
    'penalty_amount',
    'interest_rate',
    'duration_days',
    'reason',
    'status',
    'approved_at',
    'disbursed_at',
    'due_date'
];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
{
    return $this->hasMany(LoanApproval::class);
}

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }
    public function getApprovalCountAttribute()
{
    return $this->approvals()
        ->where('decision', 'approved')
        ->count();
}

public function getRemainingBalanceAttribute()
{
    return $this->total_payable
        + $this->penalty_amount
        - $this->repayments()->sum('amount');
}

public function isFullyPaid()
{
    return $this->remaining_balance <= 0;
}
}