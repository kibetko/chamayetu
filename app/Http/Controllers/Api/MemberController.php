<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Loan;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Get members of the authenticated user's active group.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | GET ACTIVE GROUP
        |--------------------------------------------------------------------------
        |
        | First try the group sent by the mobile app.
        | If none is supplied, use the user's first active group.
        |
        */

        $groupId = $request->input('group_id');

        if ($groupId) {

            $group = $user->groups()
                ->wherePivot('status', 'active')
                ->where('groups.id', $groupId)
                ->with('settings')
                ->first();

        } else {

            $group = $user->groups()
                ->wherePivot('status', 'active')
                ->with('settings')
                ->first();

        }

        /*
        |--------------------------------------------------------------------------
        | NO GROUP
        |--------------------------------------------------------------------------
        */

        if (!$group) {

            return response()->json([
                'success' => false,

                'message' => 'You are not a member of any active group.',

                'members' => [],

                'stats' => [
                    'total_members' => 0,
                    'officials' => 0,
                    'online' => 0,
                    'total_savings' => 0,
                ],
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | GET ALL GROUP MEMBERS
        |--------------------------------------------------------------------------
        */

        $members = $group->members()
            ->wherePivot('status', 'active')
            ->orderByRaw("
                CASE
                    WHEN group_members.role = 'chairperson' THEN 1
                    WHEN group_members.role = 'secretary' THEN 2
                    WHEN group_members.role = 'treasurer' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('users.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MEMBER IDS
        |--------------------------------------------------------------------------
        */

        $memberIds = $members
            ->pluck('id')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | ONLINE MEMBERS
        |--------------------------------------------------------------------------
        */

        $onlineMembers = $memberIds
            ->filter(function ($id) {

                return cache()->has(
                    'online-user-' . $id
                );

            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TOTAL SAVINGS
        |--------------------------------------------------------------------------
        */

        $totalSavings = $group->contributions()
            ->where('status', 'paid')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | OFFICIALS
        |--------------------------------------------------------------------------
        */

        $officials = $members
            ->filter(function ($member) {

                return in_array(
                    strtolower($member->pivot->role ?? 'member'),
                    [
                        'chairperson',
                        'secretary',
                        'treasurer',
                    ]
                );

            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | MEMBER DATA
        |--------------------------------------------------------------------------
        */

        $memberData = $members
            ->map(function ($member) use ($group) {

                /*
                |--------------------------------------------------------------------------
                | MEMBER CONTRIBUTIONS
                |--------------------------------------------------------------------------
                */

                $contributed = $group->contributions()
                    ->where('user_id', $member->id)
                    ->where('status', 'paid')
                    ->sum('amount');

                /*
                |--------------------------------------------------------------------------
                | MEMBER LOANS
                |--------------------------------------------------------------------------
                */

                $loans = Loan::where('group_id', $group->id)
                    ->where('user_id', $member->id)
                    ->whereIn('status', [
                        'approved',
                        'disbursed',
                        'overdue',
                        'completed',
                    ])
                    ->with('repayments')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | TOTAL PAYABLE
                |--------------------------------------------------------------------------
                */

                $totalPayable = $loans->sum(function ($loan) {

                    return (float) (
                        $loan->total_payable
                        ?? $loan->amount
                    );

                });

                /*
                |--------------------------------------------------------------------------
                | TOTAL REPAID
                |--------------------------------------------------------------------------
                */

                $totalRepaid = $loans->sum(function ($loan) {

                    return $loan->repayments->sum(
                        function ($repayment) {

                            return (float) $repayment->amount;

                        }
                    );

                });

                /*
                |--------------------------------------------------------------------------
                | OUTSTANDING
                |--------------------------------------------------------------------------
                */

                $outstanding = max(
                    0,
                    $totalPayable - $totalRepaid
                );

                /*
                |--------------------------------------------------------------------------
                | LOAN STATUS
                |--------------------------------------------------------------------------
                */

                $hasOverdueLoan = $loans
                    ->contains(function ($loan) {

                        return $loan->status === 'overdue';

                    });

                /*
                |--------------------------------------------------------------------------
                | LAST CONTRIBUTION
                |--------------------------------------------------------------------------
                */

                $lastContribution = $group->contributions()
                    ->where('user_id', $member->id)
                    ->where('status', 'paid')
                    ->latest()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | RETURN MEMBER
                |--------------------------------------------------------------------------
                */

                return [

                    'id' =>
                        (string) $member->id,

                    'name' =>
                        $member->name,

                    'email' =>
                        $member->email,

                    'phone_no' =>
                        $member->phone_no,

                    'role' =>
                        ucfirst(
                            $member->pivot->role ?? 'member'
                        ),

                    'status' =>
                        $member->pivot->status ?? 'active',

                    'joined_at' =>
                        $member->pivot->joined_at,

                    /*
                    |--------------------------------------------------------------------------
                    | ONLINE
                    |--------------------------------------------------------------------------
                    */

                    'online' =>
                        cache()->has(
                            'online-user-' . $member->id
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | CONTRIBUTIONS
                    |--------------------------------------------------------------------------
                    */

                    'contributed' =>
                        (float) $contributed,

                    /*
                    |--------------------------------------------------------------------------
                    | LOANS
                    |--------------------------------------------------------------------------
                    */

                    'loans' =>
                        $loans->sum(function ($loan) {

                            return (float) $loan->amount;

                        }),

                    'loan_repaid' =>
                        (float) $totalRepaid,

                    'outstanding' =>
                        (float) $outstanding,

                    'loan_status' =>
                        $hasOverdueLoan
                            ? 'overdue'
                            : (
                                $outstanding > 0
                                    ? 'active'
                                    : 'none'
                            ),

                    /*
                    |--------------------------------------------------------------------------
                    | LAST PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    'last_payment' =>
                        $lastContribution?->created_at,

                    'last_payment_human' =>
                        $lastContribution
                            ? $lastContribution->created_at->diffForHumans()
                            : null,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            /*
            |--------------------------------------------------------------------------
            | GROUP
            |--------------------------------------------------------------------------
            */

            'group' => [

                'id' =>
                    $group->id,

                'name' =>
                    $group->name,

                'unique_code' =>
                    $group->unique_code,

            ],

            /*
            |--------------------------------------------------------------------------
            | MEMBERS
            |--------------------------------------------------------------------------
            */

            'members' =>
                $memberData,

            /*
            |--------------------------------------------------------------------------
            | STATISTICS
            |--------------------------------------------------------------------------
            */

            'stats' => [

                'total_members' =>
                    $members->count(),

                'officials' =>
                    $officials,

                'online' =>
                    $onlineMembers->count(),

                'total_savings' =>
                    (float) $totalSavings,

            ],

        ], 200);
    }
}