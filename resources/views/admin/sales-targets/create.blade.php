@extends('layouts.admin')

@section('title', 'Buat Target Penjualan')

@section('content')
<div class="max-w-lg mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.sales-targets.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Buat Target Penjualan</h1>
            <p class="text-white/50 text-sm mt-0.5">Tentukan target harian atau bulanan</p>
        </div>
    </div>

    <div class="dark-glass-card rounded-2xl p-6 sm:p-8">
        <form action="{{ route('admin.sales-targets.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Tipe Target --}}
            <div>
                <label class="label-field">Tipe Target</label>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="daily" class="sr-only peer" 
                               {{ old('type', 'daily') === 'daily' ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border border-white/10 peer-checked:border-cyan-500/50 transition-all text-center"
                             style="background: rgba(255,255,255,0.04);">
                            <i class="fas fa-sun text-2xl text-amber-400 mb-2"></i>
                            <p class="font-semibold text-white text-sm">Harian</p>
                            <p class="text-xs text-white/40 mt-0.5">Target per hari</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="monthly" class="sr-only peer"
                               {{ old('type') === 'monthly' ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border border-white/10 peer-checked:border-cyan-500/50 transition-all text-center"
                             style="background: rgba(255,255,255,0.04);">
                            <i class="fas fa-calendar-alt text-2xl text-violet-400 mb-2"></i>
                            <p class="font-semibold text-white text-sm">Bulanan</p>
                            <p class="text-xs text-white/40 mt-0.5">Target per bulan</p>
                        </div>
                    </label>
                </div>
                @error('type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Target Amount --}}
            <div>
                <label class="label-field">Nominal Target</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40 font-semibold text-sm">Rp</span>
                    <input type="number" name="target_amount" value="{{ old('target_amount') }}" min="1000" step="1000"
                           class="input-field pl-12" placeholder="Contoh: 5000000" required>
                </div>
                @error('target_amount') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal Target --}}
            <div>
                <label class="label-field">Tanggal Target</label>
                <input type="date" name="target_date" value="{{ old('target_date', date('Y-m-d')) }}"
                       class="input-field" required>
                <p class="text-white/30 text-xs mt-1">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Untuk bulanan, gunakan tanggal pertama bulan tersebut (misal: 2026-03-01)
                </p>
                @error('target_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label class="label-field">Catatan (opsional)</label>
                <textarea name="notes" rows="2" class="input-field" 
                          placeholder="Keterangan tambahan...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                    <i class="fas fa-bullseye mr-2"></i> Buat Target
                </button>
                <a href="{{ route('admin.sales-targets.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
