<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->groups()->count() === 0) {
            return view('groups.no-group');
        }

        $activeGroupId = session('active_group_id');

        if (!$activeGroupId) {

            $activeGroupId = $user
                ->groups()
                ->first()
                ->id;

            session([
                'active_group_id' => $activeGroupId
            ]);
        }

        $group = Group::with([
            'members',
            'contributions',
            'loans',
            'settings'
        ])->findOrFail($activeGroupId);

        $stats = [

            'members' =>
                $group->members->count(),

            'contributions' =>
                $group->contributions->sum('amount'),

            'active_loans' =>
                $group->loans()
                      ->where('status', 'approved')
                      ->count(),

            'pending_requests' =>
                method_exists($group, 'joinRequests')
                    ? $group->joinRequests()
                            ->where('status', 'pending')
                            ->count()
                    : 0,
        ];

        return view('dashboard', [
            'group' => $group,
            'groups' => $user->groups,
            'stats' => $stats
        ]);
    }
}