@extends('layouts.admin')

@section('title', 'Tambah Zona Pengiriman')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Tambah Zona Pengiriman</h2>
        <p class="text-white/50 text-sm">Buat zona baru untuk wilayah pengiriman</p>
    </div>

    {{-- Form Card --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden p-6 sm:p-8">
        <form action="{{ route('admin.shipping-zones.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nama Zona --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Nama Zona</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <input type="text" name="zone_name" 
                           class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('zone_name') !border-red-400 !bg-red-500/10 @enderror"
                           value="{{ old('zone_name') }}" placeholder="Contoh: Zona A - Kota Pusat" required>
                </div>
                <p class="text-xs text-white/30 mt-1"><i class="fas fa-info-circle mr-1"></i>Nama deskriptif untuk zona</p>
                @error('zone_name') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            {{-- Wilayah Cakupan --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Wilayah Cakupan</label>
                <textarea name="areas" rows="4" 
                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 resize-none @error('areas') !border-red-400 !bg-red-500/10 @enderror"
                          placeholder="Kelurahan Sudirman, Kelurahan Gatsu, Kecamatan Denpasar Barat"
                          required>{{ old('areas') }}</textarea>
                <p class="text-xs text-white/30 mt-1"><i class="fas fa-info-circle mr-1"></i>Pisahkan dengan koma (,) untuk setiap kecamatan/kelurahan</p>
                @error('areas') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            {{-- Ongkir --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Biaya Ongkir (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 font-bold">
                        Rp
                    </span>
                    <input type="number" name="cost" 
                           class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('cost') !border-red-400 !bg-red-500/10 @enderror"
                           value="{{ old('cost') }}" min="0" placeholder="10000" required>
                </div>
                @error('cost') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3 p-4 bg-white/5 rounded-xl">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="w-5 h-5 rounded accent-cyan-500 bg-white/10 border-white/20"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm font-medium text-white cursor-pointer">
                    Aktifkan zona ini
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-white/10 mt-8">
                <a href="{{ route('admin.shipping-zones.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
                <button type="submit" class="btn-primary px-8 py-3 rounded-xl shadow-lg shadow-ocean-500/30 hover:shadow-ocean-500/50 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-save mr-2"></i> Simpan Zona
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
