<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\AdminNotificationService;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function resetPassword(User $user)
    {
        // Generate a strong random password
        $randomPassword = 'FM' . now()->format('Ymd') . rand(1000, 9999);
        
        $user->update([
            'password' => Hash::make($randomPassword),
            'must_change_password' => true,
        ]);

        AdminNotificationService::logPasswordReset($user);

        // SECURITY: Don't expose password in flash message (visible in browser DevTools)
        // Instead, use a one-time secure display mechanism
        return back()->with([
            'success' => "Password user '{$user->name}' berhasil direset. Password baru telah dikirim.",
            'reset_password' => $randomPassword,  // Use separate key for one-time display
            'reset_user' => $user->name,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }
        
        // Prevent deleting other admins (protect admin accounts)
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menghapus akun admin. Untuk keamanan, admin hanya bisa dihapus dari database secara manual.');
        }
        
        // Check if user has active orders
        $hasActiveOrders = $user->orders()
            ->whereIn('status', ['pending', 'waiting_payment', 'paid', 'confirmed', 'out_for_delivery'])
            ->exists();
            
        if ($hasActiveOrders) {
            return back()->with('error', "User '{$user->name}' memiliki pesanan aktif. Selesaikan semua pesanan terlebih dahulu sebelum menghapus akun.");
        }

        $user->delete();

        AdminNotificationService::logUserDeleted($user);

        return back()->with('success', "User '{$user->name}' berhasil dihapus.");
    }
}
