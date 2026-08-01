<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;


    protected $fillable = [

    'group_id',
    'user_id',
    'amount',
    'interest_rate',
    'duration_days',
    'total_payable',
    'due_date',
    'reason',
    'status',
    'penalty_amount',

];

protected $casts = [

    'amount'=>'decimal:2',

    'interest_rate'=>'decimal:2',

    'total_payable'=>'decimal:2',

    'penalty_amount'=>'decimal:2',

    'due_date'=>'date',

];



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * User who borrowed
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }





    /**
     * Group loan belongs to
     */
    public function group()
    {
        return $this->belongsTo(
            Group::class
        );
    }





    /**
     * Loan repayments
     */
    public function repayments()
    {
        return $this->hasMany(
            LoanRepayment::class
        );
    }





    /**
     * Loan approval records
     */
    public function approvals()
    {
        return $this->hasMany(
            LoanApproval::class
        );
    }





    /*
    |--------------------------------------------------------------------------
    | Calculated Attributes
    |--------------------------------------------------------------------------
    */



    /**
     * Total amount already paid
     *
     * Usage:
     * $loan->total_repaid
     */
    public function getTotalRepaidAttribute()
    {

        return $this->repayments()
            ->sum('amount');

    }






    /**
     * Remaining amount to pay
     *
     * Usage:
     * $loan->remaining_balance
     */
    public function getRemainingBalanceAttribute()
    {

        $balance =

            $this->total_payable

            -

            $this->total_repaid;



        return max(
            $balance,
            0
        );

    }







    /**
     * Check if loan has been fully paid
     *
     * Usage:
     * $loan->is_paid
     */
    public function getIsPaidAttribute()
    {

        return $this->remaining_balance <= 0;

    }







    /**
     * Check if loan is currently active
     *
     * Usage:
     * $loan->is_active
     */
    public function getIsActiveAttribute()
    {

        return in_array(

            $this->status,

            [
                'approved',
                'disbursed',
                'overdue'
            ]

        );

    }





    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */



    /**
     * Mark loan completed when fully paid
     */
    public function updateLoanStatus()
    {

        if($this->remaining_balance <= 0){


            $this->update([

                'status'=>'completed'

            ]);

        }


    }

}