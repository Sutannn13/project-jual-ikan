@extends('layouts.admin')

@section('title', 'Edit Template WhatsApp')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.whatsapp-templates.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Template</h1>
            <p class="text-white/50 text-sm mt-0.5">{{ $template->nama }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Form --}}
        <div class="dark-glass-card rounded-2xl p-6">
            <form action="{{ route('admin.whatsapp-templates.update', $template) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                {{-- Nama Template --}}
                <div>
                    <label class="label-field">Nama Template <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $template->nama) }}" class="input-field" required>
                    @error('nama') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="label-field">Deskripsi (opsional)</label>
                    <input type="text" name="deskripsi" value="{{ old('deskripsi', $template->deskripsi) }}" class="input-field"
                           placeholder="Keterangan singkat template ini">
                    @error('deskripsi') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pesan --}}
                <div>
                    <label class="label-field">Isi Pesan <span class="text-red-400">*</span></label>
                    <textarea name="pesan" id="pesanTmpl" rows="7" class="input-field font-mono text-sm"
                              oninput="updatePreview()" required>{{ old('pesan', $template->pesan) }}</textarea>
                    @error('pesan') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
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
                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                    <span class="text-sm text-white/70">Template aktif</span>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.whatsapp-templates.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Live Preview + Blast Button --}}
        <div class="space-y-4">
            <div class="dark-glass-card rounded-2xl p-5 sticky top-6">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-400"></i>
                    Preview Pesan
                </h3>
                <div class="rounded-xl p-3 min-h-24" style="background: rgba(37,211,102,0.06); border: 1px solid rgba(37,211,102,0.15);">
                    <p id="previewMsg" class="text-sm text-white/80 whitespace-pre-line leading-relaxed"></p>
                </div>
                <div class="mt-4 pt-4 border-t border-white/10">
                    <a href="{{ route('admin.whatsapp-templates.blast.form', $template) }}"
                       class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl text-sm font-bold text-white transition-all"
                       style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); box-shadow: 0 4px 12px rgba(37,211,102,0.3);">
                        <i class="fab fa-whatsapp text-base"></i>
                        Blast Template Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const pesanEl = document.getElementById('pesanTmpl');
const previewEl = document.getElementById('previewMsg');

function updatePreview() {
    const pesan = pesanEl.value;
    previewEl.textContent = pesan
        .replace(/{nama}/g, 'Budi Santoso')
        .replace(/{produk}/g, 'Ikan Nila')
        .replace(/{harga}/g, 'Rp 35.000/Kg')
        .replace(/{stok}/g, '25 Kg')
        .replace(/{tanggal}/g, new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}))
        .replace(/{toko}/g, 'Toko Ikan Segar') || 'Pesan akan tampil di sini...';
}

function insertVar(varName) {
    const start = pesanEl.selectionStart, end = pesanEl.selectionEnd;
    pesanEl.value = pesanEl.value.substring(0, start) + varName + pesanEl.value.substring(end);
    pesanEl.selectionStart = pesanEl.selectionEnd = start + varName.length;
    pesanEl.focus();
    updatePreview();
}

// Init preview
updatePreview();
</script>
@endpush
@endsection
