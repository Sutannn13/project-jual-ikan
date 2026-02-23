@extends('layouts.admin')

@section('title', 'Buat Template WhatsApp')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.whatsapp-templates.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Buat Template WhatsApp</h1>
            <p class="text-white/50 text-sm mt-0.5">Template pesan untuk blast ke pelanggan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Form --}}
        <div class="dark-glass-card rounded-2xl p-6">
            <form action="{{ route('admin.whatsapp-templates.store') }}" method="POST" id="templateForm" class="space-y-5">
                @csrf

                {{-- Nama Template --}}
                <div>
                    <label class="label-field">Nama Template <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" id="namaTmpl" value="{{ old('nama') }}" class="input-field" 
                           placeholder="Contoh: Promo Weekend" required>
                    @error('nama') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="label-field">Deskripsi (opsional)</label>
                    <input type="text" name="deskripsi" value="{{ old('deskripsi') }}" class="input-field" 
                           placeholder="Keterangan singkat template ini">
                    @error('deskripsi') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pesan --}}
                <div>
                    <label class="label-field">Isi Pesan <span class="text-red-400">*</span></label>
                    <textarea name="pesan" id="pesanTmpl" rows="7" class="input-field font-mono text-sm" 
                              placeholder="Ketik pesan di sini, gunakan {nama}, {produk}, dll..."
                              oninput="updatePreview()" required>{{ old('pesan') }}</textarea>
                    @error('pesan') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    {{-- Quick Insert Vars --}}
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach(['{nama}', '{produk}', '{harga}', '{stok}', '{tanggal}', '{toko}'] as $var)
                        <button type="button" onclick="insertVar('{{ $var }}')"
                                class="px-2 py-0.5 rounded text-[11px] font-mono text-cyan-300 transition-all hover:opacity-80"
                                style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
                            {{ $var }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                    <span class="text-sm text-white/70">Template aktif</span>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                        <i class="fas fa-save mr-2"></i> Simpan Template
                    </button>
                    <a href="{{ route('admin.whatsapp-templates.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Live Preview --}}
        <div class="sticky top-6">
            <div class="dark-glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-400"></i>
                    Preview Pesan
                </h3>
                {{-- WA Mockup --}}
                <div class="rounded-xl p-3 min-h-24" style="background: rgba(37,211,102,0.06); border: 1px solid rgba(37,211,102,0.15);">
                    <p id="previewMsg" class="text-sm text-white/80 whitespace-pre-line leading-relaxed">
                        Pesan akan tampil di sini...
                    </p>
                </div>
                <p class="text-xs text-white/30 mt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Variabel akan diganti dengan data nyata saat blast
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updatePreview() {
    const pesan = document.getElementById('pesanTmpl').value;
    const preview = pesan
        .replace(/{nama}/g, 'Budi Santoso')
        .replace(/{produk}/g, 'Ikan Nila')
        .replace(/{harga}/g, 'Rp 35.000/Kg')
        .replace(/{stok}/g, '25 Kg')
        .replace(/{tanggal}/g, new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}))
        .replace(/{toko}/g, 'Toko Ikan Segar');
    document.getElementById('previewMsg').textContent = preview || 'Pesan akan tampil di sini...';
}

function insertVar(varName) {
    const textarea = document.getElementById('pesanTmpl');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const value = textarea.value;
    textarea.value = value.substring(0, start) + varName + value.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + varName.length;
    textarea.focus();
    updatePreview();
}
</script>
@endpush
@endsection
