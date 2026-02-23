@php $target = $salesTarget; @endphp
@extends('layouts.admin')

@section('title', 'Edit Target Penjualan')

@section('content')
<div class="max-w-lg mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.sales-targets.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Target Penjualan</h1>
            <p class="text-white/50 text-sm mt-0.5">Ubah nominal atau tanggal target</p>
        </div>
    </div>

    {{-- Progress Summary --}}
    <div class="dark-glass-card rounded-xl p-4 mb-5 border border-white/10">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-white/70">Progres saat ini</span>
            <span class="font-bold {{ $target->is_achieved ? 'text-emerald-400' : 'text-white' }}">
                {{ number_format($target->progress_percent, 1) }}%
                @if($target->is_achieved) <i class="fas fa-trophy text-amber-400 ml-1"></i> @endif
            </span>
        </div>
        <div class="w-full h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
            <div class="h-2 rounded-full"
                 style="width: {{ min($target->progress_percent, 100) }}%;
                        background: {{ $target->is_achieved ? 'linear-gradient(90deg,#10b981,#059669)' : 'linear-gradient(90deg,#0891b2,#14b8a6)' }};"></div>
        </div>
        <p class="text-xs text-white/40 mt-2">
            Actual: Rp {{ number_format($target->actual_sales, 0, ',', '.') }} / Target: Rp {{ number_format($target->target_amount, 0, ',', '.') }}
        </p>
    </div>

    <div class="dark-glass-card rounded-2xl p-6 sm:p-8">
        <form action="{{ route('admin.sales-targets.update', $target) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            {{-- Tipe Target --}}
            <div>
                <label class="label-field">Tipe Target</label>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="daily" class="sr-only peer" 
                               {{ old('type', $target->type) === 'daily' ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border border-white/10 peer-checked:border-cyan-500/50 transition-all text-center"
                             style="background: rgba(255,255,255,0.04);">
                            <i class="fas fa-sun text-2xl text-amber-400 mb-2"></i>
                            <p class="font-semibold text-white text-sm">Harian</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="monthly" class="sr-only peer"
                               {{ old('type', $target->type) === 'monthly' ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border border-white/10 peer-checked:border-cyan-500/50 transition-all text-center"
                             style="background: rgba(255,255,255,0.04);">
                            <i class="fas fa-calendar-alt text-2xl text-violet-400 mb-2"></i>
                            <p class="font-semibold text-white text-sm">Bulanan</p>
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
                    <input type="number" name="target_amount" value="{{ old('target_amount', $target->target_amount) }}" 
                           min="1000" step="1000" class="input-field pl-12" required>
                </div>
                @error('target_amount') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="label-field">Tanggal Target</label>
                <input type="date" name="target_date" 
                       value="{{ old('target_date', $target->target_date->format('Y-m-d')) }}"
                       class="input-field" required>
                @error('target_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label class="label-field">Catatan (opsional)</label>
                <textarea name="notes" rows="2" class="input-field">{{ old('notes', $target->notes) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.sales-targets.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
