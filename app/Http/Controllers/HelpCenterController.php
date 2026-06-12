<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\HelpRequest;
use App\Models\GroupUpdate;
use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    public function index()
    {
        $group = Group::with([
            'members',
            'updates'
        ])->findOrFail(
            session('active_group_id')
        );

        $chairperson = $group->members
            ->firstWhere('pivot.role', 'chairperson');

        $secretary = $group->members
            ->firstWhere('pivot.role', 'secretary');

        $treasurer = $group->members
            ->firstWhere('pivot.role', 'treasurer');

        $updates = $group->updates()
            ->latest()
            ->get();

        return view(
    'help-center.index',
    [
        'group' => $group,
        'groups' => auth()->user()->groups,
        'chairperson' => $chairperson,
        'secretary' => $secretary,
        'treasurer' => $treasurer,
        'updates' => $updates,
    ]
);
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'issue' => 'required',
        ]);

        HelpRequest::create([
            'group_id' => session('active_group_id'),
            'user_id' => auth()->id(),
            'name' => auth()->user()->name,
            'phone' => auth()->user()->phone_no,
            'subject' => $request->subject,
            'issue' => $request->issue,
        ]);

        return back()->with(
            'success',
            'Request submitted successfully.'
        );
    }
}