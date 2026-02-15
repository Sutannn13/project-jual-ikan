@extends('layouts.admin')

@section('title', 'Riwayat Stock In')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-boxes text-teal-400 mr-2"></i> Stock In (Restok)
            </h1>
            <p class="text-white/50 text-sm mt-1">Riwayat penambahan stok produk</p>
        </div>
        <a href="{{ route('admin.stock-in.create') }}" class="btn-primary text-sm px-5 py-2.5 inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Stok
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-6">
        <div class="flex gap-3">
            <select name="produk_id" onchange="this.form.submit()"
                    class="input-field w-64">
                <option value="">Semua Produk</option>
                @foreach($produks as $produk)
                    <option value="{{ $produk->id }}" {{ request('produk_id') == $produk->id ? 'selected' : '' }}>
                        {{ $produk->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if($stockIns->isEmpty())
        <div class="dark-glass-card rounded-2xl p-12 text-center">
            <i class="fas fa-boxes text-5xl text-white/15 mb-4"></i>
            <h3 class="text-lg font-semibold text-white/60">Belum Ada Riwayat Stock In</h3>
            <p class="text-white/40 text-sm mt-2">Mulai tambah stok produk.</p>
        </div>
    @else
        <div class="dark-glass-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-white/40 border-b border-white/10">
                        <tr>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Produk</th>
                            <th class="px-5 py-4 text-right">Qty Masuk</th>
                            <th class="px-5 py-4 text-right">Stok Sebelum</th>
                            <th class="px-5 py-4 text-right">Stok Sesudah</th>
                            <th class="px-5 py-4">Supplier</th>
                            <th class="px-5 py-4">Admin</th>
                            <th class="px-5 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($stockIns as $si)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4 text-white/60">{{ $si->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-white">{{ $si->produk->nama ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="font-bold text-green-400">+{{ $si->qty }} Kg</span>
                            </td>
                            <td class="px-5 py-4 text-right text-white/50">{{ $si->stok_sebelum }} Kg</td>
                            <td class="px-5 py-4 text-right text-white/80 font-medium">{{ $si->stok_sesudah }} Kg</td>
                            <td class="px-5 py-4 text-white/50">{{ $si->supplier ?? '-' }}</td>
                            <td class="px-5 py-4 text-white/50">{{ $si->user->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-white/40 text-xs max-w-48 truncate">{{ $si->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $stockIns->links() }}</div>
    @endif
</div>
@endsection
