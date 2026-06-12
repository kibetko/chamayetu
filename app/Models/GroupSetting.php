<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupSetting extends Model
{
    use HasFactory;
    protected $fillable = [
    'group_id',
    'interest_rate',
    'repayment_period_days',
    'grace_period_days',
    'late_penalty_amount',
    'late_penalty_type',
    'minimum_contribution',
    'maximum_loan_multiplier',
    'updated_by',
    'contribution_due_day',
];
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
