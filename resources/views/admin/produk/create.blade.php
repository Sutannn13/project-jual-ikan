@extends('layouts.admin')

@section('title', 'Tambah Produk')

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
        <h2 class="text-2xl font-bold text-white">Tambah Produk Baru</h2>
        <p class="text-white/50 text-sm">Masukan informasi lengkap produk ikan Anda</p>
    </div>

    {{-- Form Card --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden p-6 sm:p-8">
        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Nama Produk --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Nama Produk</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                        <i class="fas fa-tag"></i>
                    </span>
                    <input type="text" name="nama" 
                           class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('nama') !border-red-400 !bg-red-500/10 @enderror"
                           value="{{ old('nama') }}" placeholder="Contoh: Ikan Nila Segar Super" required>
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
                            <option value="">Pilih Kategori</option>
                            <option value="Ikan Nila" {{ old('kategori') == 'Ikan Nila' ? 'selected' : '' }} class="bg-gray-800 text-white">Ikan Nila</option>
                            <option value="Ikan Mas" {{ old('kategori') == 'Ikan Mas' ? 'selected' : '' }} class="bg-gray-800 text-white">Ikan Mas</option>
                        </select>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 pointer-events-none">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                    @error('kategori') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
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
                               value="{{ old('stok') }}" min="0" placeholder="0" required>
                    </div>
                    @error('stok') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
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
                               value="{{ old('low_stock_threshold', 5) }}" min="0" placeholder="5" required>
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
                           value="{{ old('harga_per_kg') }}" min="1000" placeholder="25000" required>
                </div>
                @error('harga_per_kg') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
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
                           class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 @error('harga_modal') !border-red-400 !bg-red-500/10 @enderror"
                           value="{{ old('harga_modal', 0) }}" min="0" placeholder="18000">
                </div>
                <p class="text-xs text-white/30 mt-1"><i class="fas fa-info-circle mr-1"></i>Harga beli dari supplier</p>
                @error('harga_modal') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" 
                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 transition-all outline-none text-white font-medium placeholder-white/30 resize-none"
                          placeholder="Jelaskan kualitas dan detail produk...">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Foto Utama --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Foto Utama Produk</label>
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-teal-500 rounded-xl opacity-0 group-hover:opacity-10 transition-opacity pointer-events-none"></div>
                    <input type="file" name="foto" 
                           class="w-full px-4 py-3 bg-white/5 border border-dashed border-white/20 rounded-xl cursor-pointer hover:border-cyan-500/50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-500/15 file:text-cyan-400 hover:file:bg-cyan-500/25 transition-all text-sm text-white/50 @error('foto') !border-red-400 @enderror"
                           accept="image/*">
                </div>
                <p class="text-xs text-white/30 mt-2 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, WEBP. Maks: 5MB. Foto utama yang tampil di halaman produk.
                </p>
                @error('foto') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            {{-- Foto Tambahan --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">
                    Foto Tambahan 
                    <span class="text-xs text-cyan-400 ml-1">— opsional, bisa upload beberapa</span>
                </label>
                <div class="relative group">
                    <input type="file" name="fotos[]" id="fotosInput" multiple accept="image/*"
                           class="w-full px-4 py-3 bg-white/5 border border-dashed border-white/15 rounded-xl cursor-pointer hover:border-cyan-500/30 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-white/5 file:text-white/50 hover:file:bg-white/10 transition-all text-sm text-white/40"
                           onchange="previewAditionalPhotos(this)">
                </div>
                <div id="additionalPreview" class="flex flex-wrap gap-2 mt-2"></div>
                @error('fotos.*') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-white/10 mt-8">
                <a href="{{ route('admin.produk.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
                <button type="submit" class="btn-primary px-8 py-3 rounded-xl shadow-lg shadow-ocean-500/30 hover:shadow-ocean-500/50 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-save mr-2"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewAditionalPhotos(input) {
    const preview = document.getElementById('additionalPreview');
    preview.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `<img src="${e.target.result}" class="w-16 h-16 rounded-lg object-cover border border-white/10">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
@endsection