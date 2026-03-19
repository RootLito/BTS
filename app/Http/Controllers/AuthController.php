<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'office' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $client = Client::where('username', $request->username)
            ->where('office', $request->office)
            ->first();

        if (!$client) {
            return back()->withErrors(['username' => 'The details provided do not match our records.']);
        }

        $client->password = Hash::make($request->password);
        $client->save();

        return redirect()->route('client.login')->with('status', 'Password has been reset successfully!');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Check if the login request is coming from the Admin login page
        if ($request->is('admin-login') || $request->type === 'admin') {
            if (Auth::guard('admin')->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        // Otherwise, attempt Client login
        if (Auth::guard('client')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('client.home'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        // Logout from both guards to be safe
        Auth::guard('admin')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}