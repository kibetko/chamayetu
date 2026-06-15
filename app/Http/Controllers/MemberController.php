<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    // index: supports both /members and /groups/{group}/members
    public function index(Group $group = null)
    {
        if (!$group) {
            $groupId = session('active_group_id');
            $group = Group::with('members')->findOrFail($groupId);
        } else {
            $group->load('members');
        }

        return view('groups.members', [
            'group' => $group,
            'groups' => auth()->user()->groups,
        ]);
    }

    // show a member within a group
    public function show(Group $group, $memberId)
    {
        $member = $group->members()->where('users.id', $memberId)->firstOrFail();

        return view('groups.members.show', [
            'group' => $group,
            'groups' => auth()->user()->groups,
            'member' => $member,
        ]);
    }

    // edit form
    public function edit(Group $group, $memberId)
    {
        $member = $group->members()->where('users.id', $memberId)->firstOrFail();

        return view('groups.members.edit', [
            'group' => $group,
            'groups' => auth()->user()->groups,
            'member' => $member,
        ]);
    }

    // update member (user fields + pivot role)
    public function update(Request $request, Group $group, $memberId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_no' => 'nullable|string|max:30',
            'role' => 'required|in:member,chairperson,secretary,treasurer',
        ]);

        $user = User::findOrFail($memberId);
        $user->update($request->only(['name', 'email', 'phone_no']));

        $group->members()->updateExistingPivot($memberId, [
            'role' => $request->input('role')
        ]);

        return redirect()->route('members.show', [$group->id, $memberId])
            ->with('success', 'Member updated.');
    }

    // export members CSV
    public function export(Group $group)
    {
        $members = $group->members()->get(['users.id','users.name','users.email','users.phone_no']);

        $response = new StreamedResponse(function() use ($members) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID','Name','Email','Phone']);
            foreach ($members as $m) {
                fputcsv($handle, [$m->id, $m->name, $m->email, $m->phone_no]);
            }
            fclose($handle);
        });

        $filename = 'members-' . $group->id . '-' . date('Ymd') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        return $response;
    }

    // invite form
    public function invite(Group $group)
    {
        return view('groups.members.invite', [
            'group' => $group,
            'groups' => auth()->user()->groups,
        ]);
    }

    // handle invite POST (placeholder)
    public function inviteStore(Request $request, Group $group)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);

        // TODO: create invite record or send email
        return redirect()->route('groups.members.index', $group->id)
            ->with('success', 'Invite sent (placeholder).');
    }
}