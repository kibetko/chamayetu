<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Get members for the authenticated user's active group.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | GET GROUP
        |--------------------------------------------------------------------------
        */

        $groupId = $request->query('group_id');

        if ($groupId) {
            $group = $user->groups()
                ->where('groups.id', $groupId)
                ->wherePivot('status', 'active')
                ->first();
        } else {
            $group = $user->groups()
                ->wherePivot('status', 'active')
                ->first();
        }

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
                'members' => [],
                'stats' => [
                    'total_members' => 0,
                    'officials' => 0,
                    'online' => 0,
                    'total_savings' => 0,
                ],
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERS
        |--------------------------------------------------------------------------
        */

        $members = $group->members()
            ->wherePivot('status', 'active')
            ->get()
            ->sortBy(function ($member) {

                return match ($member->pivot->role) {

                    'chairperson' => 1,

                    'secretary' => 2,

                    'treasurer' => 3,

                    default => 4,

                };

            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | MEMBER DATA
        |--------------------------------------------------------------------------
        */

        $memberData = $members->map(function ($member) use ($group) {

            /*
            |--------------------------------------------------------------------------
            | CONTRIBUTIONS
            |--------------------------------------------------------------------------
            */

            $contributed = $group->contributions()
                ->where('user_id', $member->id)
                ->where('status', 'paid')
                ->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | LOANS
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
            | TOTAL BORROWED
            |--------------------------------------------------------------------------
            */

            $loaned = $loans->sum(function ($loan) {

                return (float) $loan->amount;

            });

            /*
            |--------------------------------------------------------------------------
            | TOTAL REPAYMENTS
            |--------------------------------------------------------------------------
            */

            $repaid = $loans->sum(function ($loan) {

                return $loan->repayments->sum(function ($repayment) {

                    return (float) $repayment->amount;

                });

            });

            /*
            |--------------------------------------------------------------------------
            | OUTSTANDING
            |--------------------------------------------------------------------------
            */

            $totalPayable = $loans->sum(function ($loan) {

                return (float) (
                    $loan->total_payable
                    ?? $loan->amount
                );

            });

            $outstanding = max(
                0,
                $totalPayable - $repaid
            );

            /*
            |--------------------------------------------------------------------------
            | ONLINE
            |--------------------------------------------------------------------------
            */

            $online = cache()->has(
                'online-user-' . $member->id
            );

            /*
            |--------------------------------------------------------------------------
            | RETURN MEMBER
            |--------------------------------------------------------------------------
            */

            return [

                'id' => $member->id,

                'name' => $member->name,

                'email' => $member->email,

                'phone_no' => $member->phone_no,

                'role' =>
                    $member->pivot->role
                    ?? 'member',

                'joined_at' =>
                    $member->pivot->joined_at,

                'online' => $online,

                'contributed' =>
                    (float) $contributed,

                'loaned' =>
                    (float) $loaned,

                'repaid' =>
                    (float) $repaid,

                'outstanding' =>
                    (float) $outstanding,

            ];

        });

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalMembers = $memberData->count();

        $officials = $memberData
            ->whereIn('role', [
                'chairperson',
                'secretary',
                'treasurer',
            ])
            ->count();

        $onlineMembers = $memberData
            ->where('online', true)
            ->count();

        $totalSavings = $memberData->sum(
            'contributed'
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'group' => [

                'id' => $group->id,

                'name' => $group->name,

                'unique_code' =>
                    $group->unique_code,

            ],

            'stats' => [

                'total_members' =>
                    $totalMembers,

                'officials' =>
                    $officials,

                'online' =>
                    $onlineMembers,

                'total_savings' =>
                    (float) $totalSavings,

            ],

            'members' =>
                $memberData,

        ]);
    }
}