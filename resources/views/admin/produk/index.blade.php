@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
{{-- Alert Messages --}}
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
        <h2 class="text-xl font-bold text-white">Data Produk</h2>
        <p class="text-sm text-white/50">Kelola semua produk ikan Anda</p>
    </div>
    <a href="{{ route('admin.produk.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>

{{-- Table Card --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-white/5">
        @forelse($produks as $produk)
        <div class="p-4">
            <div class="flex gap-3">
                @if($produk->foto)
                    <img src="{{ asset('storage/' . $produk->foto) }}" alt="Foto" 
                         class="w-16 h-16 rounded-xl object-cover border border-white/10 flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-xl bg-white/5 flex items-center justify-center text-white/20 flex-shrink-0">
                        <i class="fas fa-fish text-xl"></i>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="font-bold text-white truncate">{{ $produk->nama }}</h3>
                            <span class="{{ $produk->kategori === 'Ikan Nila' ? 'badge-nila' : 'badge-mas' }} text-[10px]">{{ $produk->kategori }}</span>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0
                            {{ $produk->stok > 5 ? 'bg-emerald-500/15 text-emerald-400' : ($produk->stok > 0 ? 'bg-amber-500/15 text-amber-400' : 'bg-red-500/15 text-red-400') }}">
                            {{ number_format($produk->stok, 1) }} Kg
                        </span>
                    </div>
                    <p class="text-cyan-400 font-extrabold mt-1">Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}<span class="text-xs text-white/40 font-normal">/Kg</span></p>
                    <div class="flex items-center gap-3 mt-3">
                        <a href="{{ route('admin.produk.edit', $produk) }}" class="text-xs text-amber-600 font-semibold">
                            <i class="fas fa-pencil-alt mr-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST"
                              onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus Produk', 'Yakin hapus produk ini? Data yang sudah dihapus tidak bisa dikembalikan.', 'danger', 'Ya, Hapus');" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 font-semibold">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-10 text-center text-white/30">
            <i class="fas fa-box-open text-4xl mb-3"></i>
            <p>Belum ada data produk.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white/5 text-white/40 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 text-left">No</th>
                    <th class="px-6 py-4 text-left">Foto</th>
                    <th class="px-6 py-4 text-left">Nama Produk</th>
                    <th class="px-6 py-4 text-left">Kategori</th>
                    <th class="px-6 py-4 text-right">Harga/Kg</th>
                    <th class="px-6 py-4 text-center">Stok</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($produks as $produk)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-6 py-4 text-white/40">{{ $loop->iteration + ($produks->currentPage() - 1) * $produks->perPage() }}</td>
                    <td class="px-6 py-4">
                        @if($produk->foto)
                            <img src="{{ asset('storage/' . $produk->foto) }}" alt="Foto" class="w-12 h-12 rounded-xl object-cover border border-white/10">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-white/20">
                                <i class="fas fa-fish"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-semibold text-white">{{ $produk->nama }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $produk->kategori === 'Ikan Nila' ? 'badge-nila' : 'badge-mas' }}">{{ $produk->kategori }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-white">Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                            {{ $produk->stok > 5 ? 'bg-emerald-500/15 text-emerald-400' : ($produk->stok > 0 ? 'bg-amber-500/15 text-amber-400' : 'bg-red-500/15 text-red-400') }}">
                            {{ number_format($produk->stok, 1) }} Kg
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.produk.edit', $produk) }}" 
                               class="w-9 h-9 rounded-lg flex items-center justify-center text-white transition-all hover:scale-105"
                               style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); box-shadow: 0 4px 10px rgba(249,115,22,0.25);">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </a>
                            <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST"
                                  onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus Produk', 'Yakin hapus produk ini? Data yang sudah dihapus tidak bisa dikembalikan.', 'danger', 'Ya, Hapus');">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-9 h-9 rounded-lg flex items-center justify-center text-white transition-all hover:scale-105"
                                        style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); box-shadow: 0 4px 10px rgba(239,68,68,0.25);">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-white/30">
                        <i class="fas fa-box-open text-4xl mb-3 block"></i>
                        <p>Belum ada data produk. Silakan tambah produk baru.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($produks->hasPages())
    <div class="px-6 py-4 border-t border-white/5">
        {{ $produks->links() }}
    </div>
    @endif
</div>
@endsection