<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan halaman lupa password (form input email).
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses request reset password:
     * 1. Validasi email ada di database
     * 2. Generate secure token
     * 3. Simpan token ke tabel password_reset_tokens
     * 4. Kirim email berisi link reset
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak terdaftar di sistem kami.',
        ]);

        $email = $request->email;

        // Throttle: cek apakah sudah request dalam 60 detik terakhir
        $recentToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subSeconds(60))
            ->first();

        if ($recentToken) {
            return back()->with('warning', 'Anda baru saja mengirim permintaan reset. Silakan tunggu 1 menit sebelum mencoba lagi.');
        }

        // Generate secure token
        $token = Str::random(64);

        // Hapus token lama untuk email ini, lalu simpan yang baru
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => hash('sha256', $token), // simpan hash-nya, bukan plain token
            'created_at' => Carbon::now(),
        ]);

        // Ambil user untuk nama
        $user = User::where('email', $email)->first();

        // Kirim email
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        Mail::to($email)->send(new ResetPasswordMail($user, $resetLink));

        return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
    }

    /**
     * Tampilkan form reset password (user klik link dari email).
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses reset password:
     * 1. Validasi token masih berlaku (60 menit)
     * 2. Validasi password baru
     * 3. Update password user
     * 4. Hapus token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.exists'        => 'Email tidak terdaftar.',
            'password.required'   => 'Password baru wajib diisi.',
            'password.min'        => 'Password minimal 6 karakter.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
        ]);

        // Cari token di database
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Token reset tidak ditemukan. Silakan request ulang.']);
        }

        // Validasi token cocok (bandingkan hash)
        if (!hash_equals($record->token, hash('sha256', $request->token))) {
            return back()->withErrors(['email' => 'Token reset tidak valid.']);
        }

        // Validasi token belum expired (60 menit)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token reset sudah kadaluarsa. Silakan request ulang.']);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password'             => bcrypt($request->password),
            'must_change_password' => false,
        ]);

        // Hapus token yang sudah dipakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }
}
