@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    {{-- Background Glow Effects --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 right-20 w-72 h-72 bg-teal-500/15 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-20 w-96 h-96 bg-ocean-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
    </div>

    <div class="relative w-full max-w-md">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center mx-auto mb-5 animate-float"
                 style="background: linear-gradient(135deg, #10b981 0%, #0891b2 100%); box-shadow: 0 15px 35px rgba(16,185,129,0.35);">
                <i class="fas fa-user-plus text-white text-2xl sm:text-3xl"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-white">Buat Akun Baru</h2>
            <p class="text-white/50 mt-2">Bergabung dengan FishMarket</p>
        </div>

        {{-- Auth Container - Glassmorphism Dark --}}
        <div class="relative rounded-3xl overflow-hidden p-6 sm:p-8"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);">
            <form action="{{ route('register.proses') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="text" name="name" 
                               class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none @error('name') !border-red-400/50 @enderror"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               value="{{ old('name') }}" placeholder="Nama Anda" required>
                    </div>
                    @error('name') 
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="email" name="email" 
                               class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none @error('email') !border-red-400/50 @enderror"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               value="{{ old('email') }}" placeholder="nama@email.com" required>
                    </div>
                    @error('email') 
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-white/80 mb-2">No. HP <span class="text-white/30 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                            <input type="text" name="no_hp" 
                                   class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none"
                                   style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                                   onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                                   onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                                   value="{{ old('no_hp') }}" placeholder="08xx">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-white/80 mb-2">Alamat <span class="text-white/30 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                            <input type="text" name="alamat" 
                                   class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none"
                                   style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                                   onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                                   onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                                   value="{{ old('alamat') }}" placeholder="Alamat">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="password" name="password" 
                               class="w-full px-4 py-3.5 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none @error('password') !border-red-400/50 @enderror"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               placeholder="Min. 6 karakter" required>
                    </div>
                    @error('password') 
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
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

                <button type="submit" class="btn-primary w-full py-3.5 text-base btn-shiny mt-2">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>

                <div class="text-center">
                    <p class="text-white/40 text-sm">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-ocean-400 font-semibold hover:text-ocean-300 transition-colors">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
        
        {{-- Trust Badge --}}
        <div class="mt-8 text-center">
            <p class="text-xs text-white/25">
                <i class="fas fa-shield-alt mr-1"></i> Data Anda aman bersama kami
            </p>
        </div>
    </div>
</div>
@endsection
