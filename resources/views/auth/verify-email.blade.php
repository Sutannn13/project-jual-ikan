@extends('layouts.auth')
@section('title', 'Verifikasi Email')
@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 right-20 w-72 h-72 bg-teal-500/15 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-20 w-96 h-96 bg-ocean-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
    </div>

    <div class="relative w-full max-w-md text-center">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 animate-float"
             style="background: linear-gradient(135deg, #10b981 0%, #0891b2 100%); box-shadow: 0 15px 35px rgba(16,185,129,0.35);">
            <i class="fas fa-envelope-open-text text-white text-3xl"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Verifikasi Email Anda</h2>
        <p class="text-white/60 mb-8">
            Kami telah mengirim link verifikasi ke alamat email Anda.<br>
            Klik link tersebut untuk mengaktifkan akun.
        </p>

        <div class="relative rounded-3xl overflow-hidden p-6 sm:p-8 mb-6"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
            
            @if(session('success'))
            <div class="mb-4 p-3 rounded-xl" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3);">
                <p class="text-emerald-400 text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </p>
            </div>
            @endif

            @if(session('warning'))
            <div class="mb-4 p-3 rounded-xl" style="background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3);">
                <p class="text-yellow-400 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </p>
            </div>
            @endif

            <div class="p-4 rounded-xl mb-4" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
                <p class="text-white/70 text-sm">
                    <i class="fas fa-info-circle text-cyan-400 mr-2"></i>
                    Belum menerima email? Cek folder <strong class="text-white">Spam/Junk</strong> atau kirim ulang link verifikasi.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-white text-sm transition-all duration-300"
                        style="background: linear-gradient(135deg, #14b8a6 0%, #0891b2 100%); box-shadow: 0 4px 15px rgba(20,184,166,0.4);">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-white/40 hover:text-white/70 text-sm transition-colors">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </button>
        </form>
    </div>
</div>
@endsection
