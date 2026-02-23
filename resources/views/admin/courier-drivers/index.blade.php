@extends('layouts.admin')

@section('title', 'Manajemen Kurir')

@section('content')
@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3">
    <i class="fas fa-check-circle text-xl"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-xl"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white">Manajemen Kurir</h2>
        <p class="text-sm text-white/50">Kelola driver pengiriman ikan</p>
    </div>
    <a href="{{ route('admin.courier-drivers.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Tambah Kurir
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="dark-glass-card rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-white">{{ $driverStats['active'] }}</p>
        <p class="text-xs text-emerald-400 font-semibold mt-1">Aktif</p>
    </div>
    <div class="dark-glass-card rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-white">{{ $driverStats['on_delivery'] }}</p>
        <p class="text-xs text-cyan-400 font-semibold mt-1">Sedang Antar</p>
    </div>
    <div class="dark-glass-card rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-white">{{ $driverStats['inactive'] }}</p>
        <p class="text-xs text-white/40 font-semibold mt-1">Nonaktif</p>
    </div>
</div>

{{-- Driver Cards --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    @if($drivers->count() > 0)
    {{-- Desktop Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Kurir</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">No. HP</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Kendaraan</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Zona</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Status</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Antar Aktif</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($drivers as $driver)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                                <i class="fas fa-motorcycle text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-white">{{ $driver->nama }}</p>
                                @if($driver->catatan)
                                <p class="text-xs text-white/40 truncate max-w-xs">{{ $driver->catatan }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $driver->no_hp) }}" target="_blank"
                           class="text-emerald-400 hover:text-emerald-300 font-semibold text-sm transition-colors">
                            {{ $driver->no_hp }}
                        </a>
                    </td>
                    <td class="p-4 text-white/70">{{ $driver->kendaraan ?? '-' }}</td>
                    <td class="p-4 text-white/70">{{ $driver->zona ?? '-' }}</td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            {{ match($driver->status) {
                                'active' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20',
                                'on_delivery' => 'bg-cyan-500/15 text-cyan-300 border border-cyan-500/20',
                                default => 'bg-white/5 text-white/40 border border-white/10'
                            } }}">
                            {{ match($driver->status) {
                                'active' => 'Aktif',
                                'on_delivery' => 'Sedang Antar',
                                default => 'Nonaktif'
                            } }}
                        </span>
                    </td>
                    <td class="p-4 text-white/70 text-center">{{ $driver->active_deliveries_count }}</td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.courier-drivers.edit', $driver) }}"
                               class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-all">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <form action="{{ route('admin.courier-drivers.destroy', $driver) }}" method="POST"
                                  onsubmit="return confirm('Hapus kurir {{ $driver->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden divide-y divide-white/5">
        @foreach($drivers as $driver)
        <div class="p-4">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                        <i class="fas fa-motorcycle text-white"></i>
                    </div>
                    <div>
                        <p class="font-bold text-white">{{ $driver->nama }}</p>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $driver->no_hp) }}" target="_blank" 
                           class="text-xs text-emerald-400">{{ $driver->no_hp }}</a>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold flex-shrink-0
                    {{ match($driver->status) {
                        'active' => 'bg-emerald-500/15 text-emerald-300',
                        'on_delivery' => 'bg-cyan-500/15 text-cyan-300',
                        default => 'bg-white/5 text-white/40'
                    } }}">
                    {{ match($driver->status) { 'active' => 'Aktif', 'on_delivery' => 'Antar', default => 'Nonaktif' } }}
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-white/50 mb-3">
                @if($driver->kendaraan)<span><i class="fas fa-car mr-1"></i>{{ $driver->kendaraan }}</span>@endif
                @if($driver->zona)<span><i class="fas fa-map-marker-alt mr-1"></i>{{ $driver->zona }}</span>@endif
                <span><i class="fas fa-box mr-1"></i>{{ $driver->active_deliveries_count }} antar aktif</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.courier-drivers.edit', $driver) }}" 
                   class="flex-1 text-center py-2 rounded-lg text-xs font-semibold text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 transition-all">
                    <i class="fas fa-pen mr-1"></i> Edit
                </a>
                <form action="{{ route('admin.courier-drivers.destroy', $driver) }}" method="POST"
                      onsubmit="return confirm('Hapus kurir ini?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-2 rounded-lg text-xs font-semibold text-red-400 bg-red-500/10 hover:bg-red-500/20 transition-all">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
             style="background: rgba(255,255,255,0.05);">
            <i class="fas fa-motorcycle text-2xl text-white/20"></i>
        </div>
        <p class="text-white/50 font-semibold mb-1">Belum ada kurir terdaftar</p>
        <p class="text-white/30 text-sm mb-4">Tambahkan kurir/driver pengiriman pertama</p>
        <a href="{{ route('admin.courier-drivers.create') }}" class="btn-primary text-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Kurir
        </a>
    </div>
    @endif
</div>
@endsection
