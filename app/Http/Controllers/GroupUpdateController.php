<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUpdate;
use Illuminate\Http\Request;
use App\Notifications\ChamaNotification;

class GroupUpdateController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $group = Group::findOrFail(
            session('active_group_id')
        );

        $path = null;

        if ($request->hasFile('attachment')) {

            $path = $request
                ->file('attachment')
                ->store(
                    'group-updates',
                    'public'
                );
        }
        

        $update = GroupUpdate::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'attachment' => $path,
        ]);
        // Notify all group members

            $members = $group->members()
                ->wherePivot('status','active')
                ->where('users.id', '!=', auth()->id())
                ->get();

            foreach ($members as $member) {

                $member->notify(
                    new ChamaNotification(
                        'New Group Update',
                        auth()->user()->name .
                        ' posted: ' .
                        $update->title,
                        url('/dashboard')
                    )
                );

            }

        return back()->with(
            'success',
            'Update published successfully.'
        );
    }
}