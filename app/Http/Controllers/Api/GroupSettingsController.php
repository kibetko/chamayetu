<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupSettingsController extends Controller
{
    /**
     * Get settings for the currently selected group.
     *
     * GET /api/group-settings?group_id=2
     *
     * Everyone in the group can access this endpoint.
     *
     * Permissions determine what React Native should display:
     *
     * Member:
     * - My Account
     * - Profile
     *
     * Secretary / Treasurer:
     * - My Account
     * - Profile
     * - Group Management
     *
     * Chairperson:
     * - My Account
     * - Profile
     * - Group Settings
     * - Group Management
     */
    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Get group ID
        |--------------------------------------------------------------------------
        */

        $groupId = $request->query('group_id');

        if (!$groupId) {
            return response()->json([
                'success' => false,
                'message' => 'Group ID is required.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Find group
        |--------------------------------------------------------------------------
        */

        $group = Group::with([
            'settings',
            'members',
        ])->find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Check membership
        |--------------------------------------------------------------------------
        */

        $membership = GroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Check membership status
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $membership->status,
            ['active', 'approved'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Your membership in this group is not active.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine role
        |--------------------------------------------------------------------------
        */

        $role = $membership->role;

        $isChairperson = $role === 'chairperson';

        $isCommittee = in_array(
            $role,
            [
                'chairperson',
                'secretary',
                'treasurer',
            ],
            true
        );

        /*
        |--------------------------------------------------------------------------
        | Leadership
        |--------------------------------------------------------------------------
        */

        $leadership = $group->members
            ->filter(function ($member) {

                return in_array(
                    $member->pivot->role,
                    [
                        'chairperson',
                        'secretary',
                        'treasurer',
                    ],
                    true
                )
                &&
                in_array(
                    $member->pivot->status ?? 'active',
                    ['active', 'approved'],
                    true
                );
            })
            ->map(function ($member) {

                return [
                    'user_id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone_no' => $member->phone_no,
                    'role' => $member->pivot->role,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            /*
            |--------------------------------------------------------------------------
            | Group
            |--------------------------------------------------------------------------
            */

            'group' => [

                'id' => $group->id,

                'name' => $group->name,

                'description' => $group->description,

                'unique_code' => $group->unique_code,

            ],

            /*
            |--------------------------------------------------------------------------
            | Current user's membership
            |--------------------------------------------------------------------------
            */

            'my_membership' => [

                'user_id' => $user->id,

                'role' => $role,

                'status' => $membership->status,

                'is_chairperson' => $isChairperson,

                'is_committee' => $isCommittee,

            ],

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            |
            | React Native uses these values to decide which
            | settings/management tabs should be visible.
            |
            */

            'permissions' => [

                /*
                | Everyone can view their own account/profile.
                */

                'view_my_account' => true,

                'view_profile' => true,

                /*
                | Group settings are chairperson-only.
                */

                'view_settings' => $isChairperson,

                'edit_settings' => $isChairperson,

                /*
                | Committee access.
                */

                'view_group_management' => $isCommittee,

                'manage_members' => $isCommittee,

                'manage_join_requests' => $isCommittee,

                'manage_invitations' => $isCommittee,

                /*
                | Leadership remains chairperson-only.
                */

                'view_leadership' => $isCommittee,

                'manage_leadership' => $isChairperson,

            ],

            /*
            |--------------------------------------------------------------------------
            | Group financial settings
            |--------------------------------------------------------------------------
            */

            'settings' => [

                'contribution_due_day' =>
                    $group->settings?->contribution_due_day,

                'minimum_contribution' =>
                    $group->settings?->minimum_contribution,

                'interest_rate' =>
                    $group->settings?->interest_rate,

                'repayment_period_days' =>
                    $group->settings?->repayment_period_days,

                'grace_period_days' =>
                    $group->settings?->grace_period_days,

                'late_penalty_amount' =>
                    $group->settings?->late_penalty_amount,

                'late_penalty_type' =>
                    $group->settings?->late_penalty_type,

                'maximum_loan_multiplier' =>
                    $group->settings?->maximum_loan_multiplier,

            ],

            /*
            |--------------------------------------------------------------------------
            | Current leadership
            |--------------------------------------------------------------------------
            */

            'leadership' => $leadership,

        ]);
    }


    /**
     * Update group financial settings.
     *
     * Only the chairperson can update these settings.
     *
     * PUT /api/group-settings
     */
    public function update(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validate request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'contribution_due_day' => [
                'required',
                'integer',
                'min:1',
                'max:31',
            ],

            'interest_rate' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'repayment_period_days' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'grace_period_days' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'late_penalty_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'late_penalty_type' => [
                'nullable',
                'in:fixed,percentage',
            ],

            'minimum_contribution' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'maximum_loan_multiplier' => [
                'nullable',
                'numeric',
                'min:1',
            ],

        ]);

        $groupId = $validated['group_id'];

        /*
        |--------------------------------------------------------------------------
        | Check membership
        |--------------------------------------------------------------------------
        */

        $membership = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Check active membership
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $membership->status,
            ['active', 'approved'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Your membership in this group is not active.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Chairperson only
        |--------------------------------------------------------------------------
        */

        if ($membership->role !== 'chairperson') {
            return response()->json([
                'success' => false,
                'message' => 'Only the chairperson can update group settings.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Find group
        |--------------------------------------------------------------------------
        */

        $group = Group::findOrFail($groupId);

        /*
        |--------------------------------------------------------------------------
        | Update settings
        |--------------------------------------------------------------------------
        */

        $settings = $group->settings()->updateOrCreate(

            [
                'group_id' => $group->id,
            ],

            [
                'contribution_due_day' =>
                    $validated['contribution_due_day'],

                'interest_rate' =>
                    $validated['interest_rate'] ?? null,

                'repayment_period_days' =>
                    $validated['repayment_period_days'] ?? null,

                'grace_period_days' =>
                    $validated['grace_period_days'] ?? null,

                'late_penalty_amount' =>
                    $validated['late_penalty_amount'] ?? null,

                'late_penalty_type' =>
                    $validated['late_penalty_type'] ?? null,

                'minimum_contribution' =>
                    $validated['minimum_contribution'] ?? null,

                'maximum_loan_multiplier' =>
                    $validated['maximum_loan_multiplier'] ?? null,

                'updated_by' =>
                    $user->id,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'Group settings updated successfully.',

            'settings' => [

                'contribution_due_day' =>
                    $settings->contribution_due_day,

                'minimum_contribution' =>
                    $settings->minimum_contribution,

                'interest_rate' =>
                    $settings->interest_rate,

                'repayment_period_days' =>
                    $settings->repayment_period_days,

                'grace_period_days' =>
                    $settings->grace_period_days,

                'late_penalty_amount' =>
                    $settings->late_penalty_amount,

                'late_penalty_type' =>
                    $settings->late_penalty_type,

                'maximum_loan_multiplier' =>
                    $settings->maximum_loan_multiplier,

            ],

        ]);
    }


    /**
     * Update group leadership.
     *
     * Only the current chairperson can change leadership.
     *
     * PUT /api/group-settings/leadership
     */
    public function updateLeadership(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validate request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'chairperson_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'secretary_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'treasurer_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

        ]);

        $groupId = $validated['group_id'];

        /*
        |--------------------------------------------------------------------------
        | Check current user's membership
        |--------------------------------------------------------------------------
        */

        $membership = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Check active membership
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $membership->status,
            ['active', 'approved'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Your membership in this group is not active.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Chairperson only
        |--------------------------------------------------------------------------
        */

        if ($membership->role !== 'chairperson') {
            return response()->json([
                'success' => false,
                'message' =>
                    'Only the chairperson can manage leadership roles.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Collect selected leadership users
        |--------------------------------------------------------------------------
        */

        $selectedUsers = array_filter([

            $validated['chairperson_id'],

            $validated['secretary_id'] ?? null,

            $validated['treasurer_id'] ?? null,

        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate people holding multiple roles
        |--------------------------------------------------------------------------
        */

        if (
            count($selectedUsers) !==
            count(array_unique($selectedUsers))
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'A member cannot hold multiple leadership positions.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Make sure all selected users are active group members
        |--------------------------------------------------------------------------
        */

        $validMemberCount = GroupMember::where(
                'group_id',
                $groupId
            )
            ->whereIn(
                'user_id',
                $selectedUsers
            )
            ->whereIn(
                'status',
                ['active', 'approved']
            )
            ->count();

        if (
            $validMemberCount !==
            count($selectedUsers)
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'All leadership members must be active members of this group.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Reset existing leadership
        |--------------------------------------------------------------------------
        */

        GroupMember::where('group_id', $groupId)
            ->whereIn(
                'role',
                [
                    'chairperson',
                    'secretary',
                    'treasurer',
                ]
            )
            ->update([
                'role' => 'member',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Assign chairperson
        |--------------------------------------------------------------------------
        */

        GroupMember::where('group_id', $groupId)
            ->where(
                'user_id',
                $validated['chairperson_id']
            )
            ->whereIn(
                'status',
                ['active', 'approved']
            )
            ->update([
                'role' => 'chairperson',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Assign secretary
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['secretary_id'])) {

            GroupMember::where('group_id', $groupId)
                ->where(
                    'user_id',
                    $validated['secretary_id']
                )
                ->whereIn(
                    'status',
                    ['active', 'approved']
                )
                ->update([
                    'role' => 'secretary',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Assign treasurer
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['treasurer_id'])) {

            GroupMember::where('group_id', $groupId)
                ->where(
                    'user_id',
                    $validated['treasurer_id']
                )
                ->whereIn(
                    'status',
                    ['active', 'approved']
                )
                ->update([
                    'role' => 'treasurer',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get updated leadership
        |--------------------------------------------------------------------------
        */

        $leadership = GroupMember::with('user')
            ->where('group_id', $groupId)
            ->whereIn(
                'role',
                [
                    'chairperson',
                    'secretary',
                    'treasurer',
                ]
            )
            ->whereIn(
                'status',
                ['active', 'approved']
            )
            ->get()
            ->map(function ($member) {

                return [

                    'user_id' =>
                        $member->user_id,

                    'name' =>
                        $member->user?->name,

                    'email' =>
                        $member->user?->email,

                    'phone_no' =>
                        $member->user?->phone_no,

                    'role' =>
                        $member->role,

                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'Leadership roles updated successfully.',

            'leadership' =>
                $leadership,

        ]);
    }
}