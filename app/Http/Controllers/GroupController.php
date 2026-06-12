<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSetting;
use App\Models\GroupJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::latest('created_at')->get();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function switch(Group $group)
    {
        $belongs = auth()
            ->user()
            ->groups()
            ->where('groups.id', $group->id)
            ->exists();

        abort_unless($belongs, 403);

        session([
            'active_group_id' => $group->id
        ]);

        return redirect()->route('dashboard');
    }

    public function joinForm()
    {
        return view('groups.join');
    }

    public function submitJoinRequest(Request $request)
    {
        $validated = $request->validate([
            'group_code' => 'required|string',
            
            'message' => 'nullable|string|max:500',
        ]);

        $group = Group::where(
            'unique_code',
            $validated['group_code']
        )->first();

        if (!$group) {
            return back()
                ->withErrors([
                    'group_code' => 'Invalid group code.'
                ])
                ->withInput();
        }

        $alreadyMember = GroupMember::where(
            'group_id',
            $group->id
        )
        ->where(
            'user_id',
            auth()->id()
        )
        ->exists();

        if ($alreadyMember) {
            return back()
                ->withErrors([
                    'group_code' => 'You are already a member of this group.'
                ]);
        }

        $pendingRequest = GroupJoinRequest::where(
            'group_id',
            $group->id
        )
        ->where(
            'user_id',
            auth()->id()
        )
        ->where(
            'status',
            'pending'
        )
        ->exists();

        if ($pendingRequest) {
            return back()
                ->withErrors([
                    'group_code' => 'You already have a pending request.'
                ]);
        }

        GroupJoinRequest::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'phone_number' => $validated['phone_number'],
            'message' => $validated['message'],
            'status' => 'pending'
        ]);

        return back()->with(
            'success',
            'Your join request has been submitted successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'unique_code' => 'required|unique:groups'
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unique_code' => strtoupper($validated['unique_code']),
            'created_by' => Auth::id(),
            'active' => true
        ]);

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'chairperson',
            'status' => 'active',
            'joined_at' => now()
        ]);

        GroupSetting::create([
            'group_id' => $group->id,
            'updated_by' => Auth::id()
        ]);

        session([
            'active_group_id' => $group->id
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Group created successfully.'
            );
    }

    public function approveJoinRequest(
        GroupJoinRequest $request
    )
    {
        $this->ensureChairperson(
            $request->group_id
        );

        GroupMember::create([
            'group_id' => $request->group_id,
            'user_id' => $request->user_id,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now()
        ]);

        $request->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return back()->with(
            'success',
            'Member approved successfully.'
        );
    }

    public function rejectJoinRequest(
        GroupJoinRequest $request
    )
    {
        $this->ensureChairperson(
            $request->group_id
        );

        $request->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return back()->with(
            'success',
            'Request rejected successfully.'
        );
    }

    public function members()
    {
        $group = auth()
            ->user()
            ->groups()
            ->with('members')
            ->findOrFail(
                session('active_group_id')
            );

        return view('groups.members', [
            'group' => $group,
            'groups' => auth()->user()->groups
        ]);
    }

   public function settings()
{
    $groupId = session('active_group_id');

    $this->ensureChairperson($groupId);

    $group = Group::with([
        'members',
        'settings'
    ])->findOrFail($groupId);

    $chairperson = $group->members
        ->firstWhere('pivot.role', 'chairperson');

    $secretary = $group->members
        ->firstWhere('pivot.role', 'secretary');

    $treasurer = $group->members
        ->firstWhere('pivot.role', 'treasurer');

    return view('groups.settings', [
        'group' => $group,
        'groups' => auth()->user()->groups,
        'chairperson' => $chairperson,
        'secretary' => $secretary,
        'treasurer' => $treasurer,
    ]);
}

    public function updateSettings(
        Request $request
    )
    {
        $groupId = session(
            'active_group_id'
        );

        $this->ensureChairperson(
            $groupId
        );

        $validated = $request->validate([
            'interest_rate' =>
                'nullable|numeric|min:0',

            'repayment_period_days' =>
                'nullable|integer|min:1',

            'grace_period_days' =>
                'nullable|integer|min:0',

            'late_penalty_amount' =>
                'nullable|numeric|min:0',

            'late_penalty_type' =>
                'nullable|in:fixed,percentage',

            'minimum_contribution' =>
                'nullable|numeric|min:0',

            'maximum_loan_multiplier' =>
                'nullable|numeric|min:1',

            'chairperson_id' =>
                'nullable|exists:users,id|different:secretary_id|different:treasurer_id',

            'secretary_id' =>
                'nullable|exists:users,id|different:chairperson_id|different:treasurer_id',

            'treasurer_id' =>
                'nullable|exists:users,id|different:chairperson_id|different:secretary_id',
        ]);

        $group = Group::findOrFail(
            $groupId
        );

        $group->settings()
            ->updateOrCreate(
                [
                    'group_id' => $group->id
                ],
                [
                    'interest_rate' => $validated['interest_rate'] ?? null,
                    'repayment_period_days' => $validated['repayment_period_days'] ?? null,
                    'grace_period_days' => $validated['grace_period_days'] ?? null,
                    'late_penalty_amount' => $validated['late_penalty_amount'] ?? null,
                    'late_penalty_type' => $validated['late_penalty_type'] ?? null,
                    'minimum_contribution' => $validated['minimum_contribution'] ?? null,
                    'maximum_loan_multiplier' => $validated['maximum_loan_multiplier'] ?? null,
                    'updated_by' => auth()->id()
                ]
            );

        GroupMember::where(
    'group_id',
    $group->id
)
->whereIn('role', [
    'chairperson',
    'secretary',
    'treasurer'
])
->update([
    'role' => 'member'
]);

$this->setRole(
    $group,
    $request->chairperson_id,
    'chairperson'
);

$this->setRole(
    $group,
    $request->secretary_id,
    'secretary'
);

$this->setRole(
    $group,
    $request->treasurer_id,
    'treasurer'
);

        return back()->with(
            'success',
            'Settings updated successfully.'
        );
    }

    private function ensureChairperson(
        int $groupId
    )
    {
        $member = GroupMember::where(
            'group_id',
            $groupId
        )
        ->where(
            'user_id',
            auth()->id()
        )
        ->first();

        if (
            !$member ||
            $member->role !== 'chairperson'
        ) {
            abort(
                403,
                'Only the chairperson can perform this action.'
            );
        }

        return $member;
    }

    private function setRole(
    Group $group,
    ?int $userId,
    string $role
)
{
    if (!$userId) {
        return;
    }

    GroupMember::where(
        'group_id',
        $group->id
    )
    ->where(
        'user_id',
        $userId
    )
    ->update([
        'role' => $role
    ]);
}
}
