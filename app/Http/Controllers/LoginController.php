<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $checkVerif = User::where('email', $request->email)->first();

        if ($checkVerif->role == 'superadmin' || $checkVerif->role == 'admin' || $checkVerif->verified_at) {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {


                $request->session()->regenerate();

                $user = User::where('email', $request->email)->first();
                $user->last_login = Carbon::now();
                $user->save();

                return redirect()->intended('dashboard');
            }
        } else if ($checkVerif->role == 'user' && !$checkVerif->verified_at) {
            return back()->withErrors([
                'email' => 'Pendaftaranmu masih diproses',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
            'password' => 'Password is incorect'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/home');
    }
}
