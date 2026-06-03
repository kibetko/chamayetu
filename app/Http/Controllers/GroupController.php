<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GroupController extends Controller
{
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'unique_code' => 'required|unique:groups',
            
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unique_code' => $validated['unique_code'],
            
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

    'allow_join_requests' =>
        $request->boolean('allow_join_requests'),

    'require_approval' => true,

    'updated_by' => Auth::id(),
]);
        

        session([
            'active_group_id' => $group->id
        ]);

        return redirect('/dashboard')
            ->with('success', 'Group created successfully');
    }
}