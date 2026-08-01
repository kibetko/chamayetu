<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();


        if ($user->groups()->count() === 0) {

            return view('groups.no-group');

        }



        $activeGroupId = session('active_group_id');


        if (! $activeGroupId) {

            $activeGroupId = $user
                ->groups()
                ->first()
                ->id;


            session([
                'active_group_id'=>$activeGroupId,
            ]);

        }



        $group = $user->groups()
            ->with([
                'members',
                'contributions',
                'loans',
                'settings',
                'joinRequests.user',
            ])
            ->where('groups.id',$activeGroupId)
            ->first();



        if(!$group){

            session()->forget('active_group_id');


            return redirect()
                ->route('groups.index')
                ->withErrors([
                    'group'=>'Invalid active group.'
                ]);

        }




        /*
        |--------------------------------------------------------------------------
        | GENERAL STATISTICS
        |--------------------------------------------------------------------------
        */


        $stats = [

    'members'=>$group->members->count(),


    // Total group contributions
    'contributions'=>$group->contributions()
        ->where('status','paid')
        ->sum('amount'),



    // Logged in user's contributions
    'my_contributions'=>$group->contributions()
        ->where('user_id',$user->id)
        ->where('status','paid')
        ->sum('amount'),



    'active_loans'=>$group->loans()
                ->whereIn('status',[

                    'approved',
                    'disbursed',
                    'overdue'

                ])
                ->count(),



            'pending_requests'=>method_exists($group,'joinRequests')

                ?

                $group->joinRequests()
                    ->where('status','pending')
                    ->count()

                :

                0,

        ];






        /*
        |--------------------------------------------------------------------------
        | LOAN STATISTICS
        |--------------------------------------------------------------------------
        */


        $loanStats = [


            'total_loaned'=>Loan::where(
                    'group_id',
                    $group->id
                )
                ->whereIn('status',[

                    'approved',
                    'disbursed',
                    'overdue',
                    'completed'

                ])
                ->sum('amount'),




            'active_loans'=>Loan::where(
                    'group_id',
                    $group->id
                )
                ->whereIn('status',[

                    'approved',
                    'disbursed',
                    'overdue'

                ])
                ->count(),




            'completed_loans'=>Loan::where(
                    'group_id',
                    $group->id
                )
                ->where('status','completed')
                ->count(),




            'overdue_loans'=>Loan::where(
                    'group_id',
                    $group->id
                )
                ->where('status','overdue')
                ->count(),

        ];






        /*
        |--------------------------------------------------------------------------
        | REPAYMENTS
        |--------------------------------------------------------------------------
        */


        $loanStats['total_repaid']

            = LoanRepayment::whereHas(

                'loan',

                function($query) use ($group){

                    $query->where(
                        'group_id',
                        $group->id
                    );

                }

            )
            ->sum('amount');





        $loanStats['outstanding']

            = Loan::where(
                'group_id',
                $group->id
            )
            ->whereIn('status',[

                'approved',
                'disbursed',
                'overdue'

            ])
            ->sum('amount')

            -

            $loanStats['total_repaid'];







        /*
        |--------------------------------------------------------------------------
        | MONTHLY LOANS
        |--------------------------------------------------------------------------
        */


        $monthlyLoans = Loan::where(
                'group_id',
                $group->id
            )
            ->whereIn('status',[

                'approved',
                'disbursed',
                'overdue',
                'completed'

            ])
            ->selectRaw("

                TO_CHAR(created_at,'Mon YYYY') as month,

                DATE_TRUNC('month',created_at) as month_date,

                SUM(amount) as total

            ")
            ->groupBy(
                'month',
                'month_date'
            )
            ->orderBy('month_date')
            ->get();







        /*
        |--------------------------------------------------------------------------
        | TOP BORROWERS
        |--------------------------------------------------------------------------
        */


        $topBorrowers = Loan::with('user')

            ->where(
                'group_id',
                $group->id
            )

            ->whereIn('status',[

                'approved',
                'disbursed',
                'overdue',
                'completed'

            ])

            ->selectRaw('

                user_id,

                SUM(amount) as total_borrowed

            ')

            ->groupBy('user_id')

            ->orderByDesc('total_borrowed')

            ->take(5)

            ->get();







        /*
        |--------------------------------------------------------------------------
        | RECOVERY RATE
        |--------------------------------------------------------------------------
        */


        $recoveryRate = 0;


        if($loanStats['total_loaned'] > 0){

            $recoveryRate =

                (

                    $loanStats['total_repaid']

                    /

                    $loanStats['total_loaned']

                )

                *100;

        }







        /*
        |--------------------------------------------------------------------------
        | PENDING REQUESTS
        |--------------------------------------------------------------------------
        */


        $pendingRequests = $group
            ->joinRequests()
            ->with('user')
            ->where('status','pending')
            ->latest()
            ->get();







        /*
        |--------------------------------------------------------------------------
        | ONLINE MEMBERS
        |--------------------------------------------------------------------------
        */


        $onlineUserIds = $this->getOnlineUsers();


        $onlineMembers = $group->members()

            ->whereIn(
                'users.id',
                $onlineUserIds
            )

            ->get();








        return view('dashboard',[


            'group'=>$group,


            'groups'=>$user->groups,


            'stats'=>$stats,


            'loanStats'=>$loanStats,


            'monthlyLoans'=>$monthlyLoans,


            'topBorrowers'=>$topBorrowers,


            'recoveryRate'=>$recoveryRate,


            'pendingRequests'=>$pendingRequests,


            'onlineMembers'=>$onlineMembers,


        ]);

    }






    private function getOnlineUsers()
    {

        $users=[];


        foreach(
            \App\Models\User::pluck('id')
            as $id
        ){

            if(
                Cache::has(
                    'online-user-'.$id
                )
            ){

                $users[]=$id;

            }

        }


        return $users;

    }

}