@extends('layouts.auth')

@section('title', 'Reset Password')

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
                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 15px 35px rgba(16,185,129,0.35);">
                <i class="fas fa-key text-white text-2xl sm:text-3xl"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-white">Buat Password Baru</h2>
            <p class="text-white/50 mt-2 text-sm">Masukkan password baru untuk akun Anda</p>
        </div>

        {{-- Auth Container - Glassmorphism Dark --}}
        <div class="relative rounded-3xl overflow-hidden p-6 sm:p-8"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);">
            
            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- Email Display --}}
                <div class="rounded-xl p-4" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-circle text-ocean-400"></i>
                        <div>
                            <p class="text-xs text-white/40">Reset password untuk</p>
                            <p class="text-sm text-white font-medium">{{ $email }}</p>
                        </div>
                    </div>
                </div>

                @error('email')
                    <div class="rounded-xl p-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3);">
                        <p class="text-red-400 text-xs flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    </div>
                @enderror

                <div>
                    <label class="block text-sm font-semibold text-white/80 mb-2">Password Baru</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-ocean-400/70"></i>
                        <input type="password" name="password" id="password"
                               class="w-full px-4 py-3.5 pl-12 pr-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none @error('password') !border-red-400/50 @enderror"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               placeholder="Min. 6 karakter" required>
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
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
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full px-4 py-3.5 pl-12 pr-12 rounded-xl text-sm text-white placeholder-white/30 transition-all duration-300 outline-none"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                               onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15), 0 4px 12px rgba(6,182,212,0.1)';"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                               placeholder="Ulangi password baru" required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Password Strength Indicator --}}
                <div id="password-strength" class="hidden">
                    <div class="flex gap-1 mb-1">
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="str-1"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="str-2"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="str-3"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="str-4"></div>
                    </div>
                    <p class="text-xs text-white/40" id="str-text"></p>
                </div>

                <button type="submit" class="btn-primary w-full py-3.5 text-base btn-shiny">
                    <i class="fas fa-save mr-2"></i> Simpan Password Baru
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
                <i class="fas fa-shield-alt mr-1"></i> Password akan dienkripsi & aman
            </p>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthDiv = document.getElementById('password-strength');

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        if (val.length === 0) {
            strengthDiv.classList.add('hidden');
            return;
        }
        strengthDiv.classList.remove('hidden');

        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;

        const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
        const labels = ['Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];

        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('str-' + i);
            bar.style.background = i <= score ? colors[score - 1] : 'rgba(255,255,255,0.1)';
        }
        document.getElementById('str-text').textContent = labels[score - 1] || 'Sangat Lemah';
        document.getElementById('str-text').style.color = colors[score - 1] || '#ef4444';
    });
</script>
@endsection
