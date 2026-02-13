@extends('layouts.admin')

@section('title', 'Edit Zona Pengiriman')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Edit Zona Pengiriman</h2>
        <p class="text-white/50 text-sm">Perbarui zona {{ $shippingZone->zone_name }}</p>
    </div>

    {{-- Form Card --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden p-6 sm:p-8">
        <form action="{{ route('admin.shipping-zones.update', $shippingZone) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            {{-- Nama Zona --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Nama Zona</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <input type="text" name="zone_name" 
                           class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30"
                           value="{{ old('zone_name', $shippingZone->zone_name) }}" required>
                </div>
            </div>

            {{-- Wilayah Cakupan --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Wilayah Cakupan</label>
                <textarea name="areas" rows="4" 
                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 resize-none"
                          required>{{ old('areas', implode(', ', $shippingZone->areas)) }}</textarea>
                <p class="text-xs text-white/30 mt-1"><i class="fas fa-info-circle mr-1"></i>Pisahkan dengan koma (,)</p>
            </div>

            {{-- Ongkir --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Biaya Ongkir (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 font-bold">
                        Rp
                    </span>
                    <input type="number" name="cost" 
                           class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30"
                           value="{{ old('cost', $shippingZone->cost) }}" min="0" required>
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3 p-4 bg-white/5 rounded-xl">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="w-5 h-5 rounded accent-cyan-500 bg-white/10 border-white/20"
                       {{ old('is_active', $shippingZone->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm font-medium text-white cursor-pointer">
                    Zona aktif
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-white/10 mt-8">
                <a href="{{ route('admin.shipping-zones.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-save mr-2"></i> Update Zona
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
