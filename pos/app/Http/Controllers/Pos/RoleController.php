<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function UserRole()
    {
        $role = User::latest()->get();
        return view('backend.user.user_role', compact('role'));
    }
}
