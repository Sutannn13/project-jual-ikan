@extends('layouts.master')

@section('title', 'Alamat Pengiriman')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('profile.show') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all flex-shrink-0">
                <i class="fas fa-arrow-left text-white/70"></i>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Alamat Pengiriman</h1>
                <p class="text-white/50 text-sm mt-0.5">Kelola alamat pengiriman Anda</p>
            </div>
        </div>
        <a href="{{ route('user.addresses.create') }}" class="btn-primary text-sm px-5 py-2.5 flex-shrink-0 w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-1"></i> Tambah Alamat
        </a>
    </div>

    @if($addresses->isEmpty())
        <div class="rounded-2xl p-12 text-center"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
            <i class="fas fa-map-marked-alt text-5xl text-white/15 mb-4"></i>
            <h3 class="text-lg font-semibold text-white/60">Belum Ada Alamat</h3>
            <p class="text-white/40 text-sm mt-2 mb-6">Tambahkan alamat pengiriman agar checkout lebih cepat.</p>
            <a href="{{ route('user.addresses.create') }}" class="btn-primary text-sm px-6 py-3">
                <i class="fas fa-plus mr-2"></i> Tambah Alamat Pertama
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($addresses as $address)
            <div class="rounded-2xl p-5 sm:p-6 group transition-all hover:border-cyan-500/30"
                 style="background: rgba(255,255,255,0.07); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.12);">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $address->is_default ? 'linear-gradient(135deg, #10b981, #059669)' : 'rgba(255,255,255,0.1)' }};">
                        <i class="fas fa-map-pin text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-white">{{ $address->label }}</span>
                            @if($address->is_default)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                    <i class="fas fa-check mr-1"></i>Alamat Utama
                                </span>
                            @endif
                        </div>
                        <p class="text-white/80 text-sm mt-1.5">
                            <i class="fas fa-user text-white/30 mr-1.5"></i>{{ $address->penerima }}
                            <span class="text-white/30 mx-2">|</span>
                            <i class="fas fa-phone text-white/30 mr-1.5"></i>{{ $address->telepon }}
                        </p>
                        <p class="text-white/50 text-sm mt-1">{{ $address->full_address }}</p>
                        @if($address->catatan)
                            <p class="text-white/30 text-xs mt-1">
                                <i class="fas fa-sticky-note mr-1"></i> {{ $address->catatan }}
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$address->is_default)
                        <form action="{{ route('user.addresses.set-default', $address) }}" method="POST">
                            @csrf
                            <button type="submit" title="Jadikan Utama"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center bg-white/10 hover:bg-green-500/20 text-white/40 hover:text-green-400 transition-all">
                                <i class="fas fa-star text-xs"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('user.addresses.edit', $address) }}" title="Edit"
                           class="w-9 h-9 rounded-lg flex items-center justify-center bg-white/10 hover:bg-blue-500/20 text-white/40 hover:text-blue-400 transition-all">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('user.addresses.destroy', $address) }}" method="POST"
                              onsubmit="event.preventDefault(); userConfirm(this, 'Hapus Alamat', 'Yakin ingin menghapus alamat ini?', 'danger', 'Ya, Hapus');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center bg-white/10 hover:bg-red-500/20 text-white/40 hover:text-red-400 transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
