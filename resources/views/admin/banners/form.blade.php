@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Banner' : 'Tambah Banner')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.banners.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
                <i class="fas fa-arrow-left text-white/70"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $isEdit ? 'Edit Banner' : 'Tambah Banner Baru' }}</h1>
                <p class="text-white/50 text-sm mt-0.5">{{ $isEdit ? 'Perbarui banner promo' : 'Buat banner promo untuk halaman toko' }}</p>
            </div>
        </div>

        <div class="dark-glass-card rounded-2xl p-6 sm:p-8">
            <form action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" 
                  method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if($isEdit) @method('PUT') @endif

                {{-- Judul --}}
                <div>
                    <label class="label-field">Judul Banner</label>
                    <input type="text" name="title" value="{{ old('title', $banner?->title) }}" 
                           class="input-field" placeholder="Contoh: Promo Lele Segar 50% OFF!" required>
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="label-field">Deskripsi (opsional)</label>
                    <textarea name="description" rows="2" class="input-field" 
                              placeholder="Keterangan singkat promo">{{ old('description', $banner?->description) }}</textarea>
                </div>

                {{-- Image --}}
                <div>
                    <label class="label-field">Gambar Banner {{ !$isEdit ? '(wajib)' : '(kosongkan jika tidak ganti)' }}</label>
                    @if($isEdit && $banner->image)
                        <div class="mb-3 rounded-xl overflow-hidden" style="max-height: 200px;">
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="Current banner" class="w-full object-cover">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="input-field" {{ !$isEdit ? 'required' : '' }}>
                    @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-white/30 text-xs mt-1">Rekomendasi: 1200x420px (rasio 16:7). Max 5MB.</p>
                </div>

                {{-- Link URL --}}
                <div>
                    <label class="label-field">Link URL (opsional)</label>
                    <input type="url" name="link_url" value="{{ old('link_url', $banner?->link_url) }}" 
                           class="input-field" placeholder="https://...">
                    <p class="text-white/30 text-xs mt-1">Jika diisi, banner akan bisa diklik menuju URL ini.</p>
                </div>

                {{-- Position & Sort --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label-field">Posisi</label>
                        <select name="position" class="input-field">
                            <option value="hero" {{ old('position', $banner?->position ?? 'hero') === 'hero' ? 'selected' : '' }}>Hero (Halaman Utama)</option>
                            <option value="catalog" {{ old('position', $banner?->position) === 'catalog' ? 'selected' : '' }}>Katalog</option>
                            <option value="sidebar" {{ old('position', $banner?->position) === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                        </select>
                    </div>
                    <div>
                        <label class="label-field">Urutan</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" 
                               class="input-field" min="0" placeholder="0">
                    </div>
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label-field">Tanggal Mulai (opsional)</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $banner?->start_date?->format('Y-m-d')) }}" 
                               class="input-field">
                    </div>
                    <div>
                        <label class="label-field">Tanggal Berakhir (opsional)</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $banner?->end_date?->format('Y-m-d')) }}" 
                               class="input-field">
                    </div>
                </div>

                {{-- Active --}}
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           class="w-4 h-4 rounded accent-cyan-500"
                           {{ old('is_active', $banner?->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-white/70 cursor-pointer">
                        <i class="fas fa-eye text-green-400 mr-1"></i> Aktif (tampilkan di toko)
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                        <i class="fas fa-save mr-2"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Banner' }}
                    </button>
                    <a href="{{ route('admin.banners.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
