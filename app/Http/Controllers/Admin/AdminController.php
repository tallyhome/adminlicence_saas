<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        $roles = $admin->roles;
        $userCount = User::where('admin_id', $admin->id)->count();
        $managedUsers = User::where('admin_id', $admin->id)->latest()->take(5)->get();

        return view('admin.users.admin_details', compact('admin', 'roles', 'userCount', 'managedUsers'));
    }
} 