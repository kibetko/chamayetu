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
            'phone_number' => 'required|string|max:20',
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
                    'group_code' => 'You already have a pending request for this group.'
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
            'allow_join_requests' => $request->boolean('allow_join_requests'),
            'require_approval' => true,
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
}