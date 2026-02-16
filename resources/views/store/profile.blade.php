@extends('layouts.master')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('home') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Profil Saya</h1>
            <p class="text-white/50 text-sm mt-0.5">Kelola informasi profil dan pengaturan akun</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Foto Profil --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl p-6 text-center"
                 style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
                <div class="relative inline-block mb-4">
                    @if($user->foto_profil)
                        <img src="{{ asset('storage/' . $user->foto_profil) }}" 
                             alt="{{ $user->name }}"
                             class="w-32 h-32 rounded-full object-cover border-4 border-white/20 shadow-xl">
                    @else
                        <div class="w-32 h-32 rounded-full flex items-center justify-center border-4 border-white/20 shadow-xl"
                             style="background: linear-gradient(135deg, rgba(6,182,212,0.3) 0%, rgba(20,184,166,0.3) 100%);">
                            <i class="fas fa-user text-4xl text-white/60"></i>
                        </div>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
                <p class="text-white/50 text-sm">{{ $user->email }}</p>
                <p class="text-xs text-white/30 mt-1">Bergabung {{ $user->created_at->translatedFormat('d M Y') }}</p>

                {{-- Upload Foto --}}
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="mt-5">
                    @csrf
                    @method('PUT')
                    <label class="block w-full px-4 py-2.5 rounded-xl text-sm font-medium cursor-pointer transition-all hover:bg-white/15"
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                        <i class="fas fa-camera mr-2 text-cyan-400"></i> Ganti Foto
                        <input type="file" name="foto_profil" class="hidden" accept="image/*"
                               onchange="this.form.submit()">
                    </label>
                </form>
                @error('foto_profil') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror

                @if($user->foto_profil)
                <form action="{{ route('profile.photo.delete') }}" method="POST" class="mt-2"
                      onsubmit="event.preventDefault(); userConfirm(this, 'Hapus Foto Profil', 'Yakin ingin menghapus foto profil kamu?', 'danger', 'Ya, Hapus');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Foto
                    </button>
                </form>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class="rounded-2xl p-5 mt-4"
                 style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
                <h4 class="text-sm font-semibold text-white/70 mb-3">Pengaturan</h4>
                <div class="space-y-1">
                    <a href="{{ route('password.change') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-white/70 hover:bg-white/10 hover:text-white transition-all">
                        <i class="fas fa-key w-5 text-center text-amber-400"></i> Ubah Password
                    </a>
                    <a href="{{ route('user.addresses.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-white/70 hover:bg-white/10 hover:text-white transition-all">
                        <i class="fas fa-map-marker-alt w-5 text-center text-green-400"></i> Alamat Pengiriman
                    </a>
                    <a href="{{ route('my.orders') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-white/70 hover:bg-white/10 hover:text-white transition-all">
                        <i class="fas fa-box w-5 text-center text-cyan-400"></i> Pesanan Saya
                    </a>
                </div>
            </div>
        </div>

        {{-- Right: Form Edit Profil --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl p-6 sm:p-8"
                 style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
                <h3 class="text-lg font-bold text-white mb-6">
                    <i class="fas fa-edit text-cyan-400 mr-2"></i> Edit Informasi Profil
                </h3>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-semibold text-white/70 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-white/30"></i>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full px-4 py-3 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all outline-none @error('name') !border-red-400/50 @enderror"
                                   style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                                   onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                                   onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                                   required>
                        </div>
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-white/70 mb-2">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-white/30"></i>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-4 py-3 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all outline-none @error('email') !border-red-400/50 @enderror"
                                   style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                                   onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                                   onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                                   required>
                        </div>
                        @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- No HP --}}
                    <div>
                        <label class="block text-sm font-semibold text-white/70 mb-2">No. HP / WhatsApp</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-white/30"></i>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" 
                                   class="w-full px-4 py-3 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all outline-none"
                                   style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                                   onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                                   onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                        @error('no_hp') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Alamat Utama --}}
                    <div>
                        <label class="block text-sm font-semibold text-white/70 mb-2">Alamat Utama</label>
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute left-4 top-3.5 text-white/30"></i>
                            <textarea name="alamat" rows="3" 
                                      class="w-full px-4 py-3 pl-12 rounded-xl text-sm text-white placeholder-white/30 transition-all outline-none resize-none"
                                      style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                                      onfocus="this.style.borderColor='rgba(6,182,212,0.5)'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.15)';"
                                      onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none';"
                                      placeholder="Jl. Contoh No. 1, Kota, Provinsi">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                        @error('alamat') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-white/30 text-xs mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Alamat ini digunakan sebagai alamat pengiriman default.
                        </p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Alamat Pengiriman Tersimpan --}}
            <div class="rounded-2xl p-6 sm:p-8 mt-6"
                 style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white">
                        <i class="fas fa-map-marker-alt text-green-400 mr-2"></i> Alamat Pengiriman
                    </h3>
                    <a href="{{ route('user.addresses.create') }}" class="btn-primary text-xs px-4 py-2">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>

                @if($user->addresses->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-map-marked-alt text-3xl text-white/20 mb-3"></i>
                        <p class="text-white/40 text-sm">Belum ada alamat pengiriman tersimpan.</p>
                        <a href="{{ route('user.addresses.create') }}" class="inline-block mt-3 text-cyan-400 text-sm hover:text-cyan-300">
                            <i class="fas fa-plus mr-1"></i> Tambah alamat pertama
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($user->addresses as $address)
                        <div class="rounded-xl p-4 flex items-start gap-3 group"
                             style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background: {{ $address->is_default ? 'linear-gradient(135deg, #10b981, #059669)' : 'rgba(255,255,255,0.1)' }};">
                                <i class="fas fa-map-pin text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-white text-sm">{{ $address->label }}</span>
                                    @if($address->is_default)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/20 text-green-400 border border-green-500/30">Utama</span>
                                    @endif
                                </div>
                                <p class="text-white/50 text-sm mt-1">{{ $address->penerima }} — {{ $address->telepon }}</p>
                                <p class="text-white/40 text-xs mt-0.5">{{ $address->alamat_lengkap }}</p>
                            </div>
                            <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('user.addresses.edit', $address) }}" 
                                   class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/10 hover:bg-blue-500/20 text-white/50 hover:text-blue-400 transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
