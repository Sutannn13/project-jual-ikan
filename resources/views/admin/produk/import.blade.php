@extends('layouts.admin')

@section('title', 'Import Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.produk.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Import Produk Massal</h1>
            <p class="text-white/50 text-sm mt-0.5">Upload file CSV atau Excel untuk tambah banyak produk sekaligus</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
        <p class="text-red-400 font-semibold text-sm mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat error saat import:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li class="text-red-300 text-xs">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Upload Form --}}
    <div class="dark-glass-card rounded-2xl p-6 sm:p-8 mb-5">
        <form action="{{ route('admin.produk.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- File Upload --}}
            <div>
                <label class="label-field">File CSV / Excel <span class="text-red-400">*</span></label>
                <div class="relative mt-1">
                    <input type="file" name="file" id="importFile" accept=".csv,.xlsx,.xls"
                           class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full"
                           onchange="updateFileName(this)" required>
                    <div id="dropZone" class="border-2 border-dashed border-white/15 rounded-xl p-8 text-center transition-all hover:border-cyan-500/40">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-3"
                             style="background: rgba(6,182,212,0.1);">
                            <i class="fas fa-file-upload text-cyan-400 text-2xl"></i>
                        </div>
                        <p id="fileNameDisplay" class="text-white/60 text-sm">
                            Klik atau drag & drop file CSV/Excel di sini
                        </p>
                        <p class="text-white/30 text-xs mt-1">Format: .csv, .xlsx, .xls — Maks: 5MB</p>
                    </div>
                </div>
                @error('file') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary px-8 py-3 text-sm font-semibold">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Import Sekarang
                </button>
                <a href="{{ route('admin.produk.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Template Download --}}
    <div class="dark-glass-card rounded-2xl p-5 mb-5">
        <h3 class="font-bold text-white text-sm mb-3 flex items-center gap-2">
            <i class="fas fa-file-csv text-emerald-400"></i>
            Template CSV
        </h3>
        <p class="text-xs text-white/50 mb-3">
            Gunakan template ini untuk memastikan format file benar. Kolom yang tersedia:
        </p>
        <div class="overflow-x-auto rounded-xl" style="background: rgba(0,0,0,0.3);">
            <table class="w-full text-xs font-mono">
                <thead>
                    <tr style="background: rgba(6,182,212,0.1);">
                        @foreach(['nama', 'kategori', 'harga_per_kg', 'harga_modal', 'stok', 'low_stock_threshold', 'deskripsi'] as $col)
                        <th class="px-3 py-2 text-left text-cyan-300 font-bold border-r border-white/5 last:border-0">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-white/5">
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">Ikan Nila Segar</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">Ikan Nila</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">35000</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">22000</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">50</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">10</td>
                        <td class="px-3 py-2 text-white/70">Ikan nila segar kualitas premium</td>
                    </tr>
                    <tr class="border-t border-white/5">
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">Ikan Mas</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">Ikan Mas</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">28000</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">18000</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">40</td>
                        <td class="px-3 py-2 text-white/70 border-r border-white/5">8</td>
                        <td class="px-3 py-2 text-white/70">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center gap-3 mt-4">
            <a href="{{ route('admin.produk.import.template') }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all"
               style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-download"></i> Download Template CSV
            </a>
        </div>
    </div>

    {{-- Panduan --}}
    <div class="dark-glass-card rounded-xl p-5">
        <h3 class="font-bold text-white text-sm mb-3 flex items-center gap-2">
            <i class="fas fa-question-circle text-cyan-400"></i>
            Panduan Import
        </h3>
        <ul class="space-y-2 text-xs text-white/60">
            <li class="flex items-start gap-2">
                <i class="fas fa-circle text-[6px] text-cyan-400 mt-1.5 flex-shrink-0"></i>
                Kolom <code class="px-1.5 py-0.5 rounded font-mono text-cyan-300" style="background:rgba(6,182,212,0.1);">nama</code> dan 
                <code class="px-1.5 py-0.5 rounded font-mono text-cyan-300" style="background:rgba(6,182,212,0.1);">harga_per_kg</code> wajib diisi.
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-circle text-[6px] text-cyan-400 mt-1.5 flex-shrink-0"></i>
                Kolom harga boleh mengandung titik (Rp) — sistem akan membersihkannya otomatis: <code class="text-cyan-300">35.000</code> atau <code class="text-cyan-300">Rp35000</code> sama-sama valid.
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-circle text-[6px] text-cyan-400 mt-1.5 flex-shrink-0"></i>
                <code class="px-1.5 py-0.5 rounded font-mono text-cyan-300" style="background:rgba(6,182,212,0.1);">kategori</code> default: <em>Lainnya</em> jika kosong.
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-circle text-[6px] text-cyan-400 mt-1.5 flex-shrink-0"></i>
                <code class="px-1.5 py-0.5 rounded font-mono text-cyan-300" style="background:rgba(6,182,212,0.1);">stok</code> default: 0 jika kosong.
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-circle text-[6px] text-cyan-400 mt-1.5 flex-shrink-0"></i>
                Baris pertama <strong class="text-white">harus</strong> merupakan header (nama kolom). Data mulai dari baris ke-2.
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-circle text-[6px] text-amber-400 mt-1.5 flex-shrink-0"></i>
                <strong class="text-amber-300">Produk dengan nama yang sama akan dilewati</strong> (tidak duplikat).
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
function updateFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    const dropZone = document.getElementById('dropZone');
    if (input.files && input.files[0]) {
        display.textContent = input.files[0].name;
        display.classList.add('text-cyan-300', 'font-semibold');
        display.classList.remove('text-white/60');
        dropZone.style.borderColor = 'rgba(6,182,212,0.5)';
        dropZone.style.background = 'rgba(6,182,212,0.05)';
    }
}
</script>
@endpush
@endsection
