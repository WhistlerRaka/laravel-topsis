<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function show()
    {
        return view('pages.user-profile');
    }

    public function update(Request $request)
    {
        $attributes = $request->validate([
            'username' => ['required', 'max:255', 'min:2'],
            'name' => ['max:100'],
            'email' => ['required', 'email', 'max:255',  Rule::unique('users')->ignore(auth()->user()->id),],
            'role' => ['max:100']
        ]);

        auth()->user()->update([
            'username' => $request->get('username'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'role' => $request->get('role')
        ]);
        return back()->with('succes', 'Profile succesfully updated');
    }

    public function showUser()
    {
        $user = User::whereNotNull('verified_at')->orWhere('role', 'superadmin')
            ->orWhere('role', 'admin')->get();

        return view('pages.user.list-user', ['user' => $user]);
    }

    public function waitingUser()
    {
        $user = User::whereNull('verified_at')->where('role', 'user')->get();

        return view('pages.user.waiting-user', ['user' => $user]);
    }

    public function acceptUser ($id)
    {
        $user = User::where('id', $id)->first();
        $user->verified_at = Carbon::now();
        $user->save();

        $data = User::whereNull('verified_at')->where('role', 'user')->get();

        return view('pages.user.waiting-user', ['user' => $data]);
    }

    public function declineUser ($id)
    {
        $user = User::where('id', $id)->first();
        $user->delete();

        $data = User::whereNull('verified_at')->where('role', 'user')->get();

        return view('pages.user.waiting-user', ['user' => $data]);
    }

    public function editUser($id)
    {
        $user = User::where('id', $id)->first();

        return view('pages.user.edit-user', ['data' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        return redirect('/user');
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();

        return redirect()->back();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8',
            're_password' => 'required_with:password|same:password',
        ]);

        $user = User::where('id', Auth::user()->id)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('succes', 'Profile succesfully updated');
    }
}
