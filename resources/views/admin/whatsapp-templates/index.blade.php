@extends('layouts.admin')

@section('title', 'Template WhatsApp')

@section('content')
@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3">
    <i class="fas fa-check-circle text-xl"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-xl"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white">Template WhatsApp Blast</h2>
        <p class="text-sm text-white/50">Kirim pesan massal ke pelanggan via WhatsApp</p>
    </div>
    <a href="{{ route('admin.whatsapp-templates.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Buat Template
    </a>
</div>

{{-- Variabel Info --}}
<div class="dark-glass-card rounded-xl p-4 mb-6 border border-emerald-500/20">
    <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background: rgba(16,185,129,0.15);">
            <i class="fas fa-info-circle text-emerald-400 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-white mb-2">Variabel yang tersedia:</p>
            <div class="flex flex-wrap gap-2">
                @foreach(['{nama}' => 'Nama pelanggan', '{produk}' => 'Nama produk', '{harga}' => 'Harga produk', '{stok}' => 'Stok tersisa', '{tanggal}' => 'Tanggal hari ini', '{toko}' => 'Nama toko'] as $var => $desc)
                <span class="px-2 py-1 rounded-lg text-xs font-mono" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                    <span class="text-cyan-300">{{ $var }}</span>
                    <span class="text-white/40 ml-1">= {{ $desc }}</span>
                </span>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Template List --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    @if($templates->count() > 0)
    <div class="divide-y divide-white/5">
        @foreach($templates as $template)
        <div class="p-5 hover:bg-white/3 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);">
                            <i class="fab fa-whatsapp text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white">{{ $template->nama }}</h3>
                            @if($template->deskripsi)
                            <p class="text-xs text-white/40">{{ $template->deskripsi }}</p>
                            @endif
                        </div>
                        @if(!$template->is_active)
                        <span class="px-2 py-0.5 rounded text-[10px] text-white/40 bg-white/5 border border-white/10 font-semibold">
                            Nonaktif
                        </span>
                        @endif
                    </div>
                    {{-- Message Preview --}}
                    <div class="rounded-xl p-3 max-w-lg" style="background: rgba(37,211,102,0.06); border: 1px solid rgba(37,211,102,0.15);">
                        <p class="text-sm text-white/75 whitespace-pre-line leading-relaxed">{{ Str::limit($template->pesan, 200) }}</p>
                    </div>
                    <p class="text-xs text-white/30 mt-2">Diperbarui {{ $template->updated_at->diffForHumans() }}</p>
                </div>
                <div class="flex sm:flex-col items-center sm:items-end gap-2">
                    <a href="{{ route('admin.whatsapp-templates.blast.form', $template) }}"
                       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all"
                       style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); box-shadow: 0 4px 12px rgba(37,211,102,0.3);">
                        <i class="fab fa-whatsapp"></i> Blast
                    </a>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.whatsapp-templates.edit', $template) }}"
                           class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-all">
                            <i class="fas fa-pen text-xs"></i>
                        </a>
                        <form action="{{ route('admin.whatsapp-templates.destroy', $template) }}" method="POST"
                              onsubmit="return confirm('Hapus template ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($templates->hasPages())
    <div class="p-4 border-t border-white/5">{{ $templates->links() }}</div>
    @endif
    @else
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
             style="background: rgba(37,211,102,0.08);">
            <i class="fab fa-whatsapp text-2xl text-emerald-400/30"></i>
        </div>
        <p class="text-white/50 font-semibold mb-1">Belum ada template</p>
        <p class="text-white/30 text-sm mb-4">Buat template pesan WhatsApp pertama Anda</p>
        <a href="{{ route('admin.whatsapp-templates.create') }}" class="btn-primary text-sm">
            <i class="fas fa-plus mr-1"></i> Buat Template
        </a>
    </div>
    @endif
</div>
@endsection
