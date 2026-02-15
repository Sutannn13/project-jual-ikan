@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-white">User Management</h2>
        <p class="text-sm text-white/50">Kelola pengguna dan reset password</p>
    </div>
</div>

<div class="dark-glass-card rounded-2xl overflow-hidden">
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-white/5">
        @forelse($users as $user)
        <div class="p-4 hover:bg-white/5 transition-colors">
            <div class="flex items-start gap-4">
                {{-- User Avatar --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $user->role === 'admin' ? 'bg-cyan-500/15' : 'bg-white/10' }}">
                    <i class="fas {{ $user->role === 'admin' ? 'fa-user-shield text-cyan-400' : 'fa-user text-white/30' }} text-sm"></i>
                </div>
                
                {{-- User Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-white truncate">{{ $user->name }}</h3>
                            <p class="text-xs text-white/40 truncate">{{ $user->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase flex-shrink-0
                            {{ $user->role === 'admin' ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/20' : 'bg-white/10 text-white/50 border border-white/10' }}">
                            {{ $user->role }}
                        </span>
                    </div>

                    @if($user->must_change_password)
                    <div class="mt-2 text-[10px] text-amber-500 flex items-center gap-1 bg-amber-500/10 px-2 py-1 rounded">
                        <i class="fas fa-exclamation-triangle"></i> Wajib ganti password
                    </div>
                    @endif

                    <div class="mt-3 flex items-center justify-between text-xs text-white/30 border-t border-white/5 pt-2">
                        <span><i class="fas fa-phone mr-1"></i> {{ $user->no_hp ?? '-' }}</span>
                        <span><i class="fas fa-calendar mr-1"></i> {{ $user->created_at->format('d M Y') }}</span>
                    </div>

                    {{-- Actions --}}
                    @if($user->id !== auth()->id())
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        <form action="{{ route('admin.users.reset', $user) }}" method="POST"
                                onsubmit="event.preventDefault(); adminConfirm(this, 'Reset Password', 'Reset password user {{ $user->name }} ke default (password123)?', 'warning', 'Ya, Reset');">
                            @csrf
                            <button type="submit" class="w-full btn-warning text-xs py-2 justify-center">
                                <i class="fas fa-key mr-1"></i> Reset Pass
                            </button>
                        </form>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus User', 'Yakin hapus user {{ $user->name }}? Semua data terkait akan ikut terhapus.', 'danger', 'Ya, Hapus');">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full btn-danger text-xs py-2 justify-center">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-white/30">
            <i class="fas fa-users text-4xl mb-3"></i>
            <p>Tidak ada data user.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white/5 text-white/40 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 text-left">No</th>
                    <th class="px-6 py-4 text-left">Nama</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-center">Role</th>
                    <th class="px-6 py-4 text-left">No. HP</th>
                    <th class="px-6 py-4 text-center">Bergabung</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($users as $user)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-6 py-4 text-white/40">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center {{ $user->role === 'admin' ? 'bg-cyan-500/15' : 'bg-white/10' }}">
                                <i class="fas {{ $user->role === 'admin' ? 'fa-user-shield text-cyan-400' : 'fa-user text-white/30' }} text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $user->name }}</p>
                                @if($user->must_change_password)
                                    <span class="text-[10px] text-amber-600 font-semibold"><i class="fas fa-exclamation-triangle"></i> Wajib ganti password</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-white/60">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-cyan-500/15 text-cyan-400' : 'bg-white/10 text-white/50' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-white/60">{{ $user->no_hp ?? '-' }}</td>
                    <td class="px-6 py-4 text-center text-xs text-white/40">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.reset', $user) }}" method="POST"
                                  onsubmit="event.preventDefault(); adminConfirm(this, 'Reset Password', 'Reset password user {{ $user->name }} ke default (password123)?', 'warning', 'Ya, Reset');">
                                @csrf
                                <button type="submit" class="btn-warning text-xs px-3 py-1.5" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                  onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus User', 'Yakin hapus user {{ $user->name }}? Semua data terkait akan ikut terhapus.', 'danger', 'Ya, Hapus');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger text-xs px-3 py-1.5" title="Hapus User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-white/30 italic">Anda</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-white/30">
                        <i class="fas fa-users text-4xl mb-3"></i>
                        <p>Tidak ada data user.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-white/5">{{ $users->links() }}</div>
    @endif
</div>
@endsection
