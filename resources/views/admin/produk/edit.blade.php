@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Alert Messages --}}
    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
            <div class="flex-1">
                <p class="font-bold mb-2">Terjadi kesalahan validasi:</p>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Edit Produk</h2>
        <p class="text-white/50 text-sm">Perbarui informasi produk {{ $produk->nama }}</p>
    </div>

    {{-- Form Card --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden p-6 sm:p-8">
        <form action="{{ route('admin.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            {{-- Nama Produk --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Nama Produk</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                        <i class="fas fa-tag"></i>
                    </span>
                    <input type="text" name="nama" 
                           class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('nama') !border-red-400 !bg-red-500/10 @enderror"
                           value="{{ old('nama', $produk->nama) }}" required>
                </div>
                @error('nama') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">Kategori</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                            <i class="fas fa-list"></i>
                        </span>
                        <select name="kategori" class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium appearance-none cursor-pointer @error('kategori') !border-red-400 !bg-red-500/10 @enderror" required>
                            <option value="Ikan Nila" {{ old('kategori', $produk->kategori) == 'Ikan Nila' ? 'selected' : '' }} class="bg-gray-800 text-white">Ikan Nila</option>
                            <option value="Ikan Mas" {{ old('kategori', $produk->kategori) == 'Ikan Mas' ? 'selected' : '' }} class="bg-gray-800 text-white">Ikan Mas</option>
                        </select>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 pointer-events-none">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                {{-- Stok --}}
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">Stok (Kg)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                            <i class="fas fa-layer-group"></i>
                        </span>
                        <input type="number" name="stok" 
                               class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('stok') !border-red-400 !bg-red-500/10 @enderror"
                               value="{{ old('stok', $produk->stok) }}" min="0" required>
                    </div>
                </div>

                {{-- Low Stock Threshold --}}
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">
                        Batas Stok Minimum (Kg)
                        <span class="text-xs text-amber-400 ml-1">— untuk notifikasi low stock</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <input type="number" name="low_stock_threshold" 
                               class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('low_stock_threshold') !border-red-400 !bg-red-500/10 @enderror"
                               value="{{ old('low_stock_threshold', $produk->low_stock_threshold ?? 5) }}" min="0" placeholder="5" required>
                    </div>
                    <p class="text-xs text-white/30 mt-1"><i class="fas fa-info-circle mr-1"></i>Anda akan diberi notifikasi jika stok mencapai batas ini</p>
                    @error('low_stock_threshold') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Harga Jual --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Harga Jual per Kg (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 font-bold">
                        Rp
                    </span>
                    <input type="number" name="harga_per_kg" 
                           class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('harga_per_kg') !border-red-400 !bg-red-500/10 @enderror"
                           value="{{ old('harga_per_kg', $produk->harga_per_kg) }}" min="1000" required>
                </div>
            </div>

            {{-- Harga Modal --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">
                    Harga Modal per Kg (Rp)
                    <span class="text-xs text-cyan-400 ml-1">— untuk hitung profit</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 font-bold">
                        Rp
                    </span>
                    <input type="number" name="harga_modal" 
                           class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30"
                           value="{{ old('harga_modal', $produk->harga_modal ?? 0) }}" min="0" placeholder="18000">
                </div>
                <p class="text-xs text-white/30 mt-1"><i class="fas fa-info-circle mr-1"></i>Harga beli dari supplier</p>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" 
                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 resize-none">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>

            {{-- Foto Upload --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Foto Produk</label>
                <div class="rounded-xl p-4 mb-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="flex items-center gap-4">
                    @if($produk->foto)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $produk->foto) }}" alt="Foto" class="w-20 h-20 rounded-lg object-cover shadow-sm">
                            <div class="absolute inset-0 bg-black/10 rounded-lg"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white/70">Foto Saat Ini</p>
                            <p class="text-xs text-white/40">Akan diganti jika Anda mengupload baru.</p>
                        </div>
                    @else
                        <div class="w-20 h-20 rounded-lg bg-white/5 flex items-center justify-center text-white/30">
                            <i class="fas fa-image text-2xl"></i>
                        </div>
                        <p class="text-sm text-white/50">Belum ada foto.</p>
                    @endif
                    </div>
                </div>
                
                <div class="relative group">
                    <input type="file" name="foto" 
                           class="w-full px-4 py-3 bg-white/5 border border-dashed border-white/20 rounded-xl cursor-pointer hover:border-cyan-500/50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-500/15 file:text-cyan-400 hover:file:bg-cyan-500/25 transition-all text-sm text-white/50"
                           accept="image/*">
                </div>
                <p class="text-xs text-white/30 mt-2 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, WEBP. Maks: 5MB.
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-white/10 mt-8">
                <a href="{{ route('admin.produk.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-save mr-2"></i> Update Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection