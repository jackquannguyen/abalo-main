<?php

namespace App\Http\Controllers;

use App\Models\AbUser;
use App\Support\AbaloSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = AbUser::where('ab_name', $credentials['username'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->ab_password)) {
            return back()->withErrors(['username' => 'Benutzername oder Passwort ist falsch.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('abalo_user_id', (int) $user->id);
        $request->session()->put('abalo_user_name', $user->ab_name);

        // Do not keep the old, unverified demo session fields.
        $request->session()->forget(['abalo_user', 'abalo_mail']);
        return redirect()->intended('/demo/offer');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function isLoggedIn(Request $request)
    {
        $id = AbaloSession::userId($request);
        return response()->json([
            'auth' => $id !== null,
            'userId' => $id,
            'user' => $id ? $request->session()->get('abalo_user_name') : null,
        ]);
    }
}
