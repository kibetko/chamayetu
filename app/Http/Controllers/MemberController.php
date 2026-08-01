<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{

    /**
     * Display group members
     * Supports:
     * /members
     * /groups/{group}/members
     */
    public function index(Group $group = null)
    {

        if (!$group) {

            $groupId = session('active_group_id');

            $group = Group::with('members')
                ->findOrFail($groupId);

        } else {

            $group->load('members');

        }



        /*
        |--------------------------------------------------------------------------
        | Members ordered by role
        |--------------------------------------------------------------------------
        */

        $members = $group->members()
            ->orderByRaw("
                CASE
                    WHEN group_user.role = 'chairperson' THEN 1
                    WHEN group_user.role = 'secretary' THEN 2
                    WHEN group_user.role = 'treasurer' THEN 3
                    ELSE 4
                END
            ")
            ->get();





        /*
        |--------------------------------------------------------------------------
        | Online Members
        |--------------------------------------------------------------------------
        */

        $onlineMembers = User::whereIn(
                'id',
                $this->getOnlineUsers()
            )
            ->whereHas('groups', function($query) use ($group){

                $query->where(
                    'groups.id',
                    $group->id
                );

            })
            ->get();






        return view('groups.members', [

            'group' => $group,

            'groups' => auth()->user()->groups,

            'members' => $members,

            'onlineMembers' => $onlineMembers,

        ]);

    }





    /**
     * Show single member
     */
    public function show(Group $group, $memberId)
    {

        $member = $group
            ->members()
            ->where('users.id',$memberId)
            ->firstOrFail();



        return view('groups.members.show',[

            'group'=>$group,

            'groups'=>auth()->user()->groups,

            'member'=>$member,

        ]);

    }





    /**
     * Edit member
     */
    public function edit(Group $group, $memberId)
    {

        $member = $group
            ->members()
            ->where('users.id',$memberId)
            ->firstOrFail();



        return view('groups.members.edit',[

            'group'=>$group,

            'groups'=>auth()->user()->groups,

            'member'=>$member,

        ]);

    }





    /**
     * Update member
     */
    public function update(
        Request $request,
        Group $group,
        $memberId
    )
    {

        $request->validate([

            'name'=>'required|string|max:255',

            'email'=>'nullable|email',

            'phone_no'=>'nullable|string|max:30',

            'role'=>'required|in:
                member,
                chairperson,
                secretary,
                treasurer',

        ]);




        $user = User::findOrFail($memberId);



        $user->update([

            'name'=>$request->name,

            'email'=>$request->email,

            'phone_no'=>$request->phone_no,

        ]);




        $group->members()
            ->updateExistingPivot(
                $memberId,
                [
                    'role'=>$request->role
                ]
            );




        return redirect()

            ->route(
                'members.show',
                [
                    $group->id,
                    $memberId
                ]
            )

            ->with(
                'success',
                'Member updated successfully.'
            );

    }





    /**
     * Export members CSV
     */
    public function export(Group $group)
    {

        $members = $group
            ->members()
            ->get([
                'users.id',
                'users.name',
                'users.email',
                'users.phone_no'
            ]);



        $response = new StreamedResponse(function() use ($members){

            $handle = fopen(
                'php://output',
                'w'
            );



            fputcsv(
                $handle,
                [
                    'ID',
                    'Name',
                    'Email',
                    'Phone'
                ]
            );



            foreach($members as $member){

                fputcsv(
                    $handle,
                    [
                        $member->id,
                        $member->name,
                        $member->email,
                        $member->phone_no
                    ]
                );

            }



            fclose($handle);

        });



        $filename =
            'members-' .
            $group->id .
            '-' .
            date('Ymd') .
            '.csv';



        $response
            ->headers
            ->set(
                'Content-Type',
                'text/csv'
            );



        $response
            ->headers
            ->set(
                'Content-Disposition',
                "attachment; filename=\"$filename\""
            );



        return $response;

    }





    /**
     * Invite page
     */
    public function invite(Group $group)
    {

        return view(
            'groups.members.invite',
            [

                'group'=>$group,

                'groups'=>auth()->user()->groups,

            ]
        );

    }





    /**
     * Store invite
     */
    public function inviteStore(
        Request $request,
        Group $group
    )
    {

        $request->validate([

            'email'=>'required|email',

            'message'=>'nullable|string|max:500',

        ]);



        /*
         Future:
         create invitation table
         send email notification
        */



        return redirect()

            ->route(
                'groups.members.index',
                $group->id
            )

            ->with(
                'success',
                'Invite sent successfully.'
            );

    }





    /**
     * Get users active recently
     */
    private function getOnlineUsers()
    {

        $users = [];



        foreach(User::pluck('id') as $id){


            if(
                cache()->has(
                    'online-user-'.$id
                )
            ){

                $users[]=$id;

            }

        }



        return $users;

    }


}