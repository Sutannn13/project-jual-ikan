@extends('layouts.admin')

@section('title', 'Manajemen Banner')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-images text-purple-400 mr-2"></i> Banner & Promo
            </h1>
            <p class="text-white/50 text-sm mt-1">Kelola spanduk promo di halaman toko</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn-primary text-sm px-5 py-2.5 inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Banner
        </a>
    </div>

    @if($banners->isEmpty())
        <div class="dark-glass-card rounded-2xl p-12 text-center">
            <i class="fas fa-images text-5xl text-white/15 mb-4"></i>
            <h3 class="text-lg font-semibold text-white/60">Belum Ada Banner</h3>
            <p class="text-white/40 text-sm mt-2">Tambahkan banner promo untuk menarik perhatian pelanggan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($banners as $banner)
            <div class="dark-glass-card rounded-2xl overflow-hidden group">
                {{-- Image --}}
                <div class="relative aspect-[16/7] overflow-hidden">
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    
                    {{-- Status Badge --}}
                    <div class="absolute top-3 right-3 flex gap-2">
                        @if($banner->isRunning())
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-500/80 text-white">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-500/80 text-white">Non-aktif</span>
                        @endif
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/80 text-white capitalize">{{ $banner->position }}</span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    <h3 class="font-bold text-white text-sm truncate">{{ $banner->title }}</h3>
                    @if($banner->description)
                        <p class="text-white/40 text-xs mt-1 line-clamp-2">{{ $banner->description }}</p>
                    @endif
                    
                    <div class="flex items-center gap-2 mt-3 text-xs text-white/30">
                        @if($banner->start_date)
                            <span>{{ $banner->start_date->format('d M') }}</span>
                            <span>—</span>
                            <span>{{ $banner->end_date?->format('d M Y') ?? 'Selamanya' }}</span>
                        @else
                            <span>Tanpa batas waktu</span>
                        @endif
                        <span class="ml-auto text-white/20">#{{ $banner->sort_order }}</span>
                    </div>

                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-white/10">
                        <a href="{{ route('admin.banners.edit', $banner) }}" 
                           class="flex-1 text-center px-3 py-2 rounded-lg text-xs font-medium bg-white/10 hover:bg-blue-500/20 text-white/60 hover:text-blue-400 transition-all">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="flex-1"
                              onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus Banner', 'Yakin hapus banner ini? Banner akan langsung hilang dari halaman utama.', 'danger', 'Ya, Hapus');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 rounded-lg text-xs font-medium bg-white/10 hover:bg-red-500/20 text-white/60 hover:text-red-400 transition-all">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $banners->links() }}</div>
    @endif
</div>
@endsection
