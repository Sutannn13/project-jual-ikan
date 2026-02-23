<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }
        return view('auth.login');
    }

    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Check email verification (skip for admin)
            if (!$user->isAdmin() && !$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('verification.notice')
                    ->with('warning', 'Silakan verifikasi email Anda sebelum login. Cek inbox/spam email Anda.');
            }

            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('warning', 'Anda harus mengganti password terlebih dahulu.');
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat Datang, Admin!');
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Login Berhasil, Selamat Belanja!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'no_hp'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'customer';

        $user = \App\Models\User::create($validated);
        Auth::login($user);

        // Fire Registered event — this triggers the email verification notification
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function processChangePassword(Request $request)
    {
        $user = Auth::user();

        // When must_change_password is true (admin reset), skip old password check
        // Otherwise require confirmation of current password
        $rules = [
            'password' => 'required|min:6|confirmed',
        ];

        if (!$user->must_change_password) {
            $rules['current_password'] = 'required';
        }

        $request->validate($rules, [
            'current_password.required' => 'Password lama wajib diisi.',
        ]);

        // Verify old password if required
        if (!$user->must_change_password) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
            }
        }

        $user->update([
            'password' => bcrypt($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('home')->with('success', 'Password berhasil diubah!');
    }

    // ==============================
    // EMAIL VERIFICATION
    // ==============================

    public function showVerificationNotice()
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }
        return view('auth.verify-email');
    }

    public function resendVerification(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        Auth::user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi telah dikirim ulang ke email Anda!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda berhasil logout.');
    }
}