<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function UserAll()
    {
        $user = User::latest()->get();
        return view('backend.user.user_all', compact('user'));
    }

    public function UserAdd()
    {
        return view('backend.user.user_add');
    }

    public function UserEdit($id)
    {
        $userEdit = User::find($id);
        return view('backend.user.user_edit', compact('userEdit'));
    }

    public function StoreUser(Request $request)
    {
        $data = new User;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->username = $request->username;
        $data->role = $request->role;
        $data->password = bcrypt($request->role);

        if ($request->file('profile_image')) {
            $file = $request->file('profile_image');

            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->profile_image = $filename;
        }
        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('user.all')->with($notification);
    }

    public function UpdateUser(Request $request, $id)
    {
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->username = $request->username;
        $data->role = $request->role;
        $data->password = bcrypt($request->role);

        if ($request->file('profile_image')) {
            $file = $request->file('profile_image');


            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->profile_image = $filename;
        }
        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('user.all')->with($notification);
    }

    public function UserDelete($id)
    {
        $userDelete = User::find($id);
        $userDelete->delete();

        $notification = array(
            'message' => 'User Profile Deleted Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('user.all')->with($notification);
    }
}
