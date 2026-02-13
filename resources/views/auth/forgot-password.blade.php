@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    {{-- Background Glow Effects --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 left-10 sm:left-20 w-72 h-72 bg-ocean-500/15 rounded-full blur-3xl animate-pulse-glow"></div>
        <div class="absolute bottom-20 right-10 sm:right-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center mx-auto mb-5 animate-float"
                 style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 15px 35px rgba(245,158,11,0.35);">
                <i class="fas fa-envelope-open-text text-white text-2xl sm:text-3xl"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-white">Lupa Password?</h2>
            <p class="text-white/50 mt-2 text-sm">Tenang, masukkan email Anda dan kami akan kirimkan<br>link untuk reset password.</p>
        </div>

        {{-- Auth Container - Glassmorphism Dark --}}
        <div class="relative rounded-3xl overflow-hidden p-6 sm:p-8"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);">
            
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Info Box --}}
                <div class="rounded-xl p-4" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-ocean-400 mt-0.5"></i>
                        <p class="text-xs text-white/60 leading-relaxed">
                            Kami akan mengirim link reset password ke email yang Anda daftarkan. Link berlaku selama <strong class="text-white/80">60 menit</strong>.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="email" name="email" 
                               class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none @error('email') !border-red-400/50 !ring-red-400/20 @enderror"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               value="{{ old('email') }}" placeholder="Masukkan email yang terdaftar" required autofocus>
                    </div>
                    @error('email')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full py-3.5 text-base btn-shiny">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Link Reset
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-white/40 text-sm hover:text-white/60 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left text-xs"></i> Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>

        {{-- Trust Badge --}}
        <div class="mt-8 text-center">
            <p class="text-xs text-white/25">
                <i class="fas fa-shield-alt mr-1"></i> Email Anda aman & tidak akan dibagikan
            </p>
        </div>
    </div>
</div>
@endsection
