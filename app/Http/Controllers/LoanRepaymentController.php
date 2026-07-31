<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;


class LoanRepaymentController extends Controller
{


public function store(Request $request, Loan $loan)
{

    $request->validate([

        'amount'=>'required|numeric|min:1'

    ]);



    LoanRepayment::create([

        'loan_id'=>$loan->id,

        'user_id'=>auth()->id(),

        'amount'=>$request->amount,

        'payment_method'=>'manual',

        'paid_at'=>now()

    ]);



    if($loan->remaining_balance <=0){

        $loan->update([

            'status'=>'completed'

        ]);

    }


    return back()
    ->with('success','Repayment recorded');

}


}