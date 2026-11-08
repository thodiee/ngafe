@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                    <i class="fa-solid fa-users text-sm"></i>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Pegawai & User</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data seluruh staf kafe, hak akses sistem, dan struktur penggajian.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openAddModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Pegawai</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Akun</div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $users->total() }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kasir & Waiter</div>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $users->whereIn('role', ['cashier', 'waiter'])->count() }}
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Dapur & Gudang</div>
            <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                {{ $users->whereIn('role', ['kitchen', 'inventory'])->count() }}
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Admin & Owner</div>
            <div class="text-xl sm:text-2xl font-black text-brand-600 dark:text-brand-400 mt-1">
                {{ $users->whereIn('role', ['admin', 'owner'])->count() }}
            </div>
        </div>
    </div>

    <!-- Users Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[680px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Pegawai</th>
                        <th class="p-4 sm:px-6">Email / Akses</th>
                        <th class="p-4 sm:px-6">Role Jabatan</th>
                        <th class="p-4 sm:px-6">Struktur Gaji</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($users as $user)
                    @php
                        $roleColors = [
                            'admin'     => 'bg-purple-500/15 text-purple-600 dark:text-purple-300 border-purple-500/30',
                            'owner'     => 'bg-brand-500/15 text-brand-600 dark:text-brand-300 border-brand-500/30',
                            'cashier'   => 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-300 border-emerald-500/30',
                            'waiter'    => 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-300 border-cyan-500/30',
                            'kitchen'   => 'bg-orange-500/15 text-orange-600 dark:text-orange-300 border-orange-500/30',
                            'inventory' => 'bg-blue-500/15 text-blue-600 dark:text-blue-300 border-blue-500/30',
                            'finance'   => 'bg-teal-500/15 text-teal-600 dark:text-teal-300 border-teal-500/30',
                            'hr'        => 'bg-rose-500/15 text-rose-600 dark:text-rose-300 border-rose-500/30',
                        ];
                        $roleColor = $roleColors[$user->role] ?? 'bg-slate-500/15 text-slate-600 dark:text-slate-300 border-slate-500/30';
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500/20 to-brand-600/30 text-brand-600 dark:text-brand-400 font-bold flex items-center justify-center border border-brand-500/30 shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 sm:px-6">
                            <div class="text-slate-700 dark:text-slate-300 font-mono text-xs">{{ $user->email }}</div>
                        </td>
                        <td class="p-4 sm:px-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $roleColor }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6">
                            <div class="space-y-0.5 text-xs">
                                <div class="font-bold text-slate-800 dark:text-slate-200">
                                    Rp {{ number_format($user->base_salary, 0, ',', '.') }} <span class="font-normal text-slate-400">/bln</span>
                                </div>
                                <div class="text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                    <span>Makan: Rp {{ number_format($user->daily_allowance, 0, ',', '.') }}/hari</span>
                                    <span>•</span>
                                    <span>Denda Telat: Rp {{ number_format($user->lateness_penalty, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editUser({{ $user->toJson() }})" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit Pegawai">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus Pegawai">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-user-slash text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data pegawai yang terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<!-- Modal Tambah/Edit Pegawai -->
<div id="addModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 my-8 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-brand-500/15 text-brand-500 flex items-center justify-center border border-brand-500/30">
                    <i class="fa-solid fa-user-gear"></i>
                </span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modalTitle">Tambah Pegawai</h3>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" id="userForm" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Email (Login User)</label>
                    <input type="email" name="email" id="email" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" placeholder="budi@ngafe.id" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Role Jabatan</label>
                <select name="role" id="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" required>
                    <option value="admin">Admin (Full Control)</option>
                    <option value="owner">Owner (Laporan & Monitoring)</option>
                    <option value="cashier">Kasir (POS & Pembayaran)</option>
                    <option value="waiter">Waiter (Pelayan Meja)</option>
                    <option value="kitchen">Dapur (Kitchen Display)</option>
                    <option value="inventory">Gudang (Bahan Baku & Mutasi)</option>
                    <option value="finance">Keuangan (Biaya & Laba Rugi)</option>
                    <option value="hr">HRD (Absensi & Shift)</option>
                </select>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-800 pt-4 mt-2">
                <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Informasi Kompensasi & Gaji</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">Gaji Pokok Bulanan (Rp)</label>
                        <input type="number" name="base_salary" id="base_salary" value="0" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" required>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">Uang Makan / Hari (Rp)</label>
                            <input type="number" name="daily_allowance" id="daily_allowance" value="0" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">Potongan Telat / Hari (Rp)</label>
                            <input type="number" name="lateness_penalty" id="lateness_penalty" value="0" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" required>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Password Akun <span class="text-xs font-normal text-slate-400 lowercase">(kosongkan jika tidak diubah saat edit)</span>
                </label>
                <input type="password" name="password" id="password" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition" placeholder="Minimal 6 karakter">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-600/20 active:scale-95 transition text-sm">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex');
        document.getElementById('userForm').reset();
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('modalTitle').innerText = "Tambah Pegawai Baru";
        document.getElementById('password').required = true;
    }

    function closeModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addModal').classList.remove('flex');
        document.getElementById('userForm').reset();
    }

    function editUser(user) {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex');
        document.getElementById('modalTitle').innerText = "Edit Pegawai: " + user.name;
        document.getElementById('userForm').action = "/admin/users/" + user.id;
        document.getElementById('formMethod').value = "POST";
        
        document.getElementById('name').value = user.name || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('role').value = user.role || 'cashier';
        document.getElementById('base_salary').value = user.base_salary || 0;
        document.getElementById('daily_allowance').value = user.daily_allowance || 0;
        document.getElementById('lateness_penalty').value = user.lateness_penalty || 0;
        
        document.getElementById('password').required = false;
    }
</script>
@endsection