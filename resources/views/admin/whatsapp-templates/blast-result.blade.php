@extends('layouts.admin')

@section('title', 'Hasil Blast WhatsApp')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.whatsapp-templates.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Hasil Blast WhatsApp</h1>
            <p class="text-white/50 text-sm mt-0.5">Klik link untuk membuka chat WhatsApp</p>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="dark-glass-card rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold text-white">{{ count($links) }}</p>
            <p class="text-xs text-white/50 mt-1">Total Link</p>
        </div>
        <div class="dark-glass-card rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold text-emerald-400">{{ $log->target_count ?? count($links) }}</p>
            <p class="text-xs text-white/50 mt-1">Penerima</p>
        </div>
        <div class="dark-glass-card rounded-xl p-4 text-center">
            <p class="text-sm font-bold text-cyan-300">{{ $template->nama }}</p>
            <p class="text-xs text-white/50 mt-1">Template</p>
        </div>
        <div class="dark-glass-card rounded-xl p-4 text-center">
            <p class="text-sm font-bold text-white">{{ now()->format('H:i') }}</p>
            <p class="text-xs text-white/50 mt-1">{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Tips --}}
    <div class="p-4 rounded-xl mb-5" style="background: rgba(6,182,212,0.06); border: 1px solid rgba(6,182,212,0.15);">
        <div class="flex items-start gap-3">
            <i class="fas fa-lightbulb text-cyan-400 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-white mb-1">Cara menggunakan:</p>
                <ol class="text-xs text-white/60 space-y-1 list-decimal list-inside">
                    <li>Klik tombol <strong class="text-white">Buka WhatsApp</strong> di setiap baris</li>
                    <li>WhatsApp akan terbuka dengan pesan yang sudah terisi</li>
                    <li>Klik <strong class="text-white">Kirim</strong> di WhatsApp untuk mengirim</li>
                    <li>Gunakan <strong class="text-white">Buka Semua</strong> untuk membuka sekaligus (pop-up mungkin diblokir browser)</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Open All Button --}}
    @if(count($links) > 0)
    <div class="flex items-center gap-3 mb-4">
        <button onclick="openAll()" 
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); box-shadow: 0 4px 12px rgba(37,211,102,0.3);">
            <i class="fab fa-whatsapp"></i> Buka Semua ({{ count($links) }})
        </button>
        <span class="text-xs text-white/40">Pop-up harus diizinkan di browser</span>
    </div>
    @endif

    {{-- Links List --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden">
        @if(count($links) > 0)
        <div class="divide-y divide-white/5">
            @foreach($links as $index => $item)
            <div class="p-4 flex items-center justify-between gap-4 hover:bg-white/3 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white/50"
                         style="background: rgba(255,255,255,0.06);">
                        {{ $index + 1 }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-white text-sm">{{ $item['name'] ?? 'Pelanggan' }}</p>
                        <p class="text-xs text-white/40">{{ $item['phone'] ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ $item['url'] }}" target="_blank"
                   class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all flex-shrink-0 hover:scale-105"
                   style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);">
                    <i class="fab fa-whatsapp"></i>
                    <span class="hidden sm:inline">Buka</span>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-3xl text-white/20 mb-3"></i>
            <p class="text-white/50">Tidak ada penerima yang ditemukan</p>
            <p class="text-xs text-white/30 mt-1">Pastikan pelanggan memiliki nomor HP yang valid</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const links = @json(array_column($links ?? [], 'url'));

function openAll() {
    links.forEach((url, i) => {
        setTimeout(() => window.open(url, '_blank'), i * 300);
    });
}
</script>
@endpush
@endsection
