@extends('layouts.auth')

@section('title', 'Ubah Password')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    {{-- Background Glow Effects --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-ocean-500/15 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-float"
                 style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 15px 35px rgba(245,158,11,0.35);">
                <i class="fas fa-key text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Ubah Password</h2>
            <p class="text-sm text-white/50 mt-1">Password Anda telah direset. Silakan buat password baru.</p>
        </div>

        {{-- Auth Container - Glassmorphism Dark --}}
        <div class="relative rounded-3xl overflow-hidden p-8"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);">
            <form action="{{ route('password.change.proses') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Password Baru</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="password" name="password" 
                               class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none @error('password') !border-red-400/50 @enderror"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               placeholder="Min. 6 karakter" required>
                    </div>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="password" name="password_confirmation" 
                               class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               placeholder="Ulangi password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3 text-base btn-shiny">
                    <i class="fas fa-save mr-2"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
