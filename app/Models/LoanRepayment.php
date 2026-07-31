<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [

    'loan_id',
    'amount',
    'payment_method',
    'reference',
    'paid_at'

];


    protected $casts = [

        'paid_at'=>'datetime'

    ];


    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }


}