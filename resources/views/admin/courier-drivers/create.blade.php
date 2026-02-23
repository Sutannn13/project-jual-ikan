@extends('layouts.admin')

@section('title', 'Tambah Kurir')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.courier-drivers.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Tambah Kurir</h1>
            <p class="text-white/50 text-sm mt-0.5">Daftarkan driver pengiriman baru</p>
        </div>
    </div>

    <div class="dark-glass-card rounded-2xl p-6 sm:p-8">
        <form action="{{ route('admin.courier-drivers.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama --}}
            <div>
                <label class="label-field">Nama Kurir <span class="text-red-400">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="input-field" 
                       placeholder="Nama lengkap" required>
                @error('nama') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- No HP --}}
            <div>
                <label class="label-field">Nomor HP / WhatsApp <span class="text-red-400">*</span></label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="input-field" 
                       placeholder="08xxxxxxxxxx" required>
                @error('no_hp') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kendaraan --}}
            <div>
                <label class="label-field">Jenis Kendaraan</label>
                <input type="text" name="kendaraan" value="{{ old('kendaraan') }}" class="input-field" 
                       placeholder="Motor / Mobil / dll (opsional)">
                @error('kendaraan') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Zona --}}
            <div>
                <label class="label-field">Zona / Area Pengiriman</label>
                <input type="text" name="zona" value="{{ old('zona') }}" class="input-field" 
                       placeholder="Contoh: Dalam Kota, Luar Kota (opsional)">
                @error('zona') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="label-field">Status</label>
                <select name="status" class="input-field">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="on_delivery" {{ old('status') === 'on_delivery' ? 'selected' : '' }}>Sedang Antar</option>
                </select>
                @error('status') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label class="label-field">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" class="input-field" 
                          placeholder="Info tambahan tentang kurir...">{{ old('catatan') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                    <i class="fas fa-motorcycle mr-2"></i> Tambah Kurir
                </button>
                <a href="{{ route('admin.courier-drivers.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
