@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Pegawai / Users</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Pegawai</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama</th>
                <th class="p-4">Email / Username</th>
                <th class="p-4">Role</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">
                    {{ $user->name }}
                    <div class="text-xs text-slate-400 font-normal mt-1">Gaji Pokok: Rp {{ number_format($user->base_salary, 0, ',', '.') }}</div>
                </td>
                <td class="p-4">{{ $user->email }}</td>
                <td class="p-4"><span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs font-bold uppercase">{{ $user->role }}</span></td>
                <td class="p-4 text-right">
                    <button onclick="editUser({{ $user->toJson() }})" class="text-brand-400 hover:text-brand-300 ml-2"><i class="fa-solid fa-edit"></i></button>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>

<!-- Modal Tambah/Edit -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4" id="modalTitle">Tambah Pegawai</h3>
        <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama</label>
                <input type="text" name="name" id="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Email (Sebagai Username)</label>
                <input type="email" name="email" id="email" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Role</label>
                <select name="role" id="role" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    <option value="admin">Admin</option>
                    <option value="owner">Owner</option>
                    <option value="cashier">Kasir</option>
                    <option value="waiter">Waiter (Pelayan)</option>
                    <option value="kitchen">Dapur (Kitchen)</option>
                    <option value="inventory">Gudang (Inventory)</option>
                    <option value="finance">Keuangan (Finance)</option>
                    <option value="hr">HRD</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Gaji Pokok (Rp)</label>
                <input type="number" name="base_salary" id="base_salary" value="0" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm mb-1 text-slate-400">Uang Makan / Hari</label>
                    <input type="number" name="daily_allowance" id="daily_allowance" value="0" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
                <div>
                    <label class="block text-sm mb-1 text-slate-400">Potongan Telat / Hari</label>
                    <input type="number" name="lateness_penalty" id="lateness_penalty" value="0" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Password <span class="text-xs text-slate-500">(kosongkan jika tidak diubah saat edit)</span></label>
                <input type="password" name="password" id="password" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('userForm').reset();
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('modalTitle').innerText = "Tambah Pegawai";
        document.getElementById('password').required = true;
    }

    function editUser(user) {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = "Edit Pegawai";
        document.getElementById('userForm').action = "/admin/users/" + user.id;
        document.getElementById('formMethod').value = "POST"; // using standard POST route
        
        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        document.getElementById('base_salary').value = user.base_salary;
        document.getElementById('daily_allowance').value = user.daily_allowance;
        document.getElementById('lateness_penalty').value = user.lateness_penalty;
        
        document.getElementById('password').required = false;
    }
</script>
@endsection