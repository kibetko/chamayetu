<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
{
    $group = Group::with('members')
        ->findOrFail(session('active_group_id'));

    return view('groups.members', [
        'group' => $group,
        'groups' => auth()->user()->groups,
    ]);

}}