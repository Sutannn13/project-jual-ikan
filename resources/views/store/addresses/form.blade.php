@extends('layouts.master')

@section('title', $isEdit ? 'Edit Alamat' : 'Tambah Alamat')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('user.addresses.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $isEdit ? 'Edit Alamat' : 'Tambah Alamat Baru' }}</h1>
            <p class="text-white/50 text-sm mt-0.5">{{ $isEdit ? 'Perbarui informasi alamat pengiriman' : 'Simpan alamat baru untuk pengiriman' }}</p>
        </div>
    </div>

    <div class="rounded-2xl p-6 sm:p-8"
         style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
        <form action="{{ $isEdit ? route('user.addresses.update', $address) : route('user.addresses.store') }}" 
              method="POST" class="space-y-5">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- Label --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Label Alamat</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Rumah', 'Kantor', 'Kolam', 'Toko', 'Gudang'] as $lbl)
                    <label class="cursor-pointer">
                        <input type="radio" name="label" value="{{ $lbl }}" class="hidden peer"
                               {{ old('label', $address->label ?? 'Rumah') === $lbl ? 'checked' : '' }}>
                        <span class="inline-block px-4 py-2 rounded-xl text-sm font-medium transition-all
                                     peer-checked:bg-cyan-500/30 peer-checked:text-cyan-300 peer-checked:border-cyan-500/50
                                     bg-white/5 text-white/50 border border-white/10 hover:bg-white/10">
                            {{ $lbl }}
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nama Penerima & Telepon --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">Nama Penerima</label>
                    <input type="text" name="penerima" value="{{ old('penerima', $address->penerima ?? Auth::user()->name) }}" 
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                           onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                           placeholder="Nama lengkap penerima" required>
                    @error('penerima') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">No. Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $address->telepon ?? Auth::user()->no_hp) }}" 
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                           onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                           placeholder="08xxxxxxxxxx" required>
                    @error('telepon') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Alamat Lengkap --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" rows="3" 
                          class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none resize-none"
                          style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                          onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                          onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                          placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan"
                          required>{{ old('alamat_lengkap', $address->alamat_lengkap ?? '') }}</textarea>
                @error('alamat_lengkap') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kecamatan, Kota, Provinsi --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $address->kecamatan ?? '') }}" 
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                           onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                           placeholder="Kecamatan">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">Kota/Kabupaten</label>
                    <input type="text" name="kota" value="{{ old('kota', $address->kota ?? '') }}" 
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                           onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                           placeholder="Kota">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-white/70 mb-2">Kode Pos</label>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $address->kode_pos ?? '') }}" 
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                           onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                           placeholder="12345" maxlength="10">
                </div>
            </div>

            {{-- Provinsi --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Provinsi</label>
                <input type="text" name="provinsi" value="{{ old('provinsi', $address->provinsi ?? '') }}" 
                       class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                       onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                       onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                       placeholder="Jawa Timur">
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-semibold text-white/70 mb-2">Catatan / Patokan <span class="text-white/30">(opsional)</span></label>
                <textarea name="catatan" rows="2" 
                          class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none resize-none"
                          style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                          onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                          onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                          placeholder="Dekat masjid, gang ke-2 sebelah kanan, dll.">{{ old('catatan', $address->catatan ?? '') }}</textarea>
            </div>

            {{-- Default --}}
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
                <input type="checkbox" name="is_default" value="1" id="is_default"
                       class="w-4 h-4 rounded accent-cyan-500"
                       {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                <label for="is_default" class="text-sm text-white/70 cursor-pointer">
                    <i class="fas fa-star text-amber-400 mr-1"></i> Jadikan alamat utama (default untuk checkout)
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                    <i class="fas fa-save mr-2"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Alamat' }}
                </button>
                <a href="{{ route('user.addresses.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
