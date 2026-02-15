@extends('layouts.admin')

@section('title', 'Tambah Stok')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.stock-in.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
                <i class="fas fa-arrow-left text-white/70"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Tambah Stok (Stock In)</h1>
                <p class="text-white/50 text-sm mt-0.5">Tambah stok produk tanpa mengubah data produk lainnya</p>
            </div>
        </div>

        <div class="dark-glass-card rounded-2xl p-6 sm:p-8">
            <form action="{{ route('admin.stock-in.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Pilih Produk --}}
                <div>
                    <label class="label-field">Pilih Produk</label>
                    <select name="produk_id" class="input-field" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produks as $produk)
                            <option value="{{ $produk->id }}" {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                                {{ $produk->nama }} — Stok saat ini: {{ $produk->stok }} Kg
                            </option>
                        @endforeach
                    </select>
                    @error('produk_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah Stok Masuk --}}
                <div>
                    <label class="label-field">Jumlah Stok Masuk (Kg)</label>
                    <input type="number" name="qty" value="{{ old('qty') }}" step="0.5" min="0.5" max="10000"
                           class="input-field" placeholder="Contoh: 50" required>
                    @error('qty') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-white/30 text-xs mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Stok akan ditambahkan ke stok yang sudah ada (bukan menimpa).
                    </p>
                </div>

                {{-- Harga Modal --}}
                <div>
                    <label class="label-field">Harga Modal per Kg (opsional)</label>
                    <input type="number" name="harga_modal" value="{{ old('harga_modal') }}" min="0"
                           class="input-field" placeholder="Kosongkan jika sama dengan harga modal sebelumnya">
                    @error('harga_modal') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-white/30 text-xs mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Akan update harga modal produk jika diisi.
                    </p>
                </div>

                {{-- Supplier --}}
                <div>
                    <label class="label-field">Supplier (opsional)</label>
                    <input type="text" name="supplier" value="{{ old('supplier') }}" 
                           class="input-field" placeholder="Nama pemasok ikan">
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="label-field">Catatan (opsional)</label>
                    <textarea name="catatan" rows="2" class="input-field" 
                              placeholder="Keterangan tambahan (mis: stok pagi, kondisi prima)">{{ old('catatan') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                        <i class="fas fa-plus mr-2"></i> Tambah Stok
                    </button>
                    <a href="{{ route('admin.stock-in.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
