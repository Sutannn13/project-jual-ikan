@extends('layouts.admin')

@section('title', 'Zona Pengiriman')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Zona Pengiriman & Ongkir</h2>
            <p class="text-white/50 text-sm mt-1">Kelola area dan biaya pengiriman berdasarkan wilayah</p>
        </div>
        <a href="{{ route('admin.shipping-zones.create') }}" class="btn-primary px-5 py-2.5 rounded-xl shadow-lg">
            <i class="fas fa-plus mr-2"></i> Tambah Zona
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="dark-glass-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/50 text-xs font-semibold uppercase tracking-wider">Total Zona</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $zones->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 flex items-center justify-center">
                    <i class="fas fa-map-marked-alt text-cyan-400 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dark-glass-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/50 text-xs font-semibold uppercase tracking-wider">Zona Aktif</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $zones->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500/20 to-green-600/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dark-glass-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/50 text-xs font-semibold uppercase tracking-wider">Ongkir Rata-rata</p>
                    <p class="text-3xl font-bold text-white mt-1">Rp {{ number_format($zones->where('is_active', true)->avg('cost') ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 flex items-center justify-center">
                    <i class="fas fa-truck text-amber-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Zones List --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-lg font-bold text-white">Daftar Zona</h3>
        </div>

        @if($zones->isEmpty())
            <div class="p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-white/30 text-3xl"></i>
                </div>
                <p class="text-white/50 mb-4">Belum ada zona pengiriman</p>
                <a href="{{ route('admin.shipping-zones.create') }}" class="btn-primary px-6 py-2.5 rounded-xl inline-block">
                    <i class="fas fa-plus mr-2"></i> Tambah Zona Pertama
                </a>
            </div>
        @else
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-white/5">
        @forelse($zones as $zone)
        <div class="p-4 hover:bg-white/5 transition-colors">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h3 class="font-bold text-white">{{ $zone->zone_name }}</h3>
                    <p class="text-xs text-white/40 mt-0.5">{{ count($zone->areas) }} kecamatan tercover</p>
                </div>
                @if($zone->is_active)
                    <span class="px-2 py-1 bg-green-500/10 text-green-400 text-[10px] font-bold uppercase rounded-lg border border-green-500/20">
                        Aktif
                    </span>
                @else
                    <span class="px-2 py-1 bg-gray-500/10 text-gray-400 text-[10px] font-bold uppercase rounded-lg border border-gray-500/20">
                        Nonaktif
                    </span>
                @endif
            </div>

            <div class="space-y-3">
                {{-- Ongkir --}}
                <div class="flex justify-between items-center bg-white/5 p-2.5 rounded-lg border border-white/5">
                    <span class="text-xs text-white/60">Biaya Ongkir</span>
                    <span class="font-bold text-cyan-400">Rp {{ number_format($zone->cost, 0, ',', '.') }}</span>
                </div>

                {{-- Areas --}}
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-white/40 font-semibold mb-2">Wilayah Cakupan</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(array_slice($zone->areas ?? [], 0, 5) as $area)
                            <span class="px-2 py-1 bg-white/10 text-white/70 text-xs rounded-md border border-white/5">{{ $area }}</span>
                        @endforeach
                        @if(count($zone->areas ?? []) > 5)
                            <span class="px-2 py-1 bg-white/5 text-white/40 text-xs rounded-md border border-white/5">+{{ count($zone->areas) - 5 }} lainnya</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-2 mt-2 border-t border-white/5">
                    <a href="{{ route('admin.shipping-zones.edit', $zone) }}" class="flex-1 btn-secondary text-xs py-2 justify-center">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" 
                          onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus Zona Pengiriman', 'Yakin hapus zona {{ $zone->zone_name }}? Ongkir terkait akan ikut terhapus.', 'danger', 'Ya, Hapus');" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full btn-danger text-xs py-2 justify-center">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-white/30">
            <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
            <p>Belum ada zona pengiriman.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-white/5">
                <tr class="text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-white/70 uppercase tracking-wider">Nama Zona</th>
                    <th class="px-6 py-4 text-xs font-semibold text-white/70 uppercase tracking-wider">Wilayah Cakupan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-white/70 uppercase tracking-wider">Ongkir</th>
                    <th class="px-6 py-4 text-xs font-semibold text-white/70 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-white/70 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($zones as $zone)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-white">{{ $zone->zone_name }}</p>
                        <p class="text-xs text-white/40 mt-0.5">{{ count($zone->areas) }} area</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($zone->areas, 0, 3) as $area)
                                <span class="px-2 py-1 bg-cyan-500/10 text-cyan-400 text-xs rounded-md">{{ $area }}</span>
                            @endforeach
                            @if(count($zone->areas) > 3)
                                <span class="px-2 py-1 bg-white/10 text-white/50 text-xs rounded-md">+{{ count($zone->areas) - 3 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-white">Rp {{ number_format($zone->cost, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($zone->is_active)
                            <span class="px-2.5 py-1 bg-green-500/10 text-green-400 text-xs font-semibold rounded-full">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-gray-500/10 text-gray-400 text-xs font-semibold rounded-full">
                                <i class="fas fa-times-circle mr-1"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.shipping-zones.edit', $zone) }}" 
                               class="w-9 h-9 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 flex items-center justify-center transition-all">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" 
                                  onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus Zona Pengiriman', 'Yakin hapus zona {{ $zone->zone_name }}? Ongkir terkait akan ikut terhapus.', 'danger', 'Ya, Hapus');">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-9 h-9 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 flex items-center justify-center transition-all">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        @endif
    </div>
</div>
@endsection
