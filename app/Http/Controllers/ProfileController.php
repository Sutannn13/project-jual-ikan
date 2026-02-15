<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil user
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('addresses');
        return view('store.profile', compact('user'));
    }

    /**
     * Update profil user (nama, email, no_hp, alamat)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
        ], [
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        // Refresh auth user session agar navbar langsung update
        Auth::setUser($user->fresh());

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update foto profil
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'foto_profil.max' => 'Ukuran foto maksimal 2MB.',
            'foto_profil.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('profil', 'public');
        $user->update(['foto_profil' => $path]);

        // Refresh auth user session
        Auth::setUser($user->fresh());

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Hapus foto profil
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->update(['foto_profil' => null]);

        // Refresh auth user session
        Auth::setUser($user->fresh());

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
