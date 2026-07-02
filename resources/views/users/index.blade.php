<x-fleet-layout title="Pengurusan Pengguna">
    <div class="page-header">
        <h1>Pengurusan Pengguna</h1>
        <p>Urus hak akses pengguna sistem</p>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:10px;flex:1">
            <input type="text" name="search" class="form-control search-input" placeholder="Cari nama, emel, no. pekerja..." value="{{ request('search') }}">
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addUserModal').classList.add('open')"><x-icon name="plus" :size="16" /> Tambah Pengguna</button>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>Nama</th><th>Jawatan</th><th>Jabatan</th><th>Peranan</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                @php
                    $roleName = $u->roles->first()?->name ?? 'staff';
                    $roleColors = ['admin' => 'badge-purple', 'fleet' => 'badge-info', 'guard' => 'badge-warn', 'staff' => 'badge-neutral'];
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="avatar" style="width:28px;height:28px;font-size:10px;{{ $roleName === 'fleet' ? 'background:#00b894' : ($roleName === 'guard' ? 'background:#6c5ce7' : ($roleName === 'staff' ? 'background:#e17055' : '')) }}">{{ $u->avatar_initials }}</div>
                            <div>
                                <div style="font-weight:600">{{ $u->name }}</div>
                                <div style="font-size:11px;color:var(--c-muted)">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $u->position ?? '—' }}</td>
                    <td>{{ $u->department ?? '—' }}</td>
                    <td><span class="badge-pill {{ $roleColors[$roleName] ?? 'badge-neutral' }}">{{ ucfirst($roleName) }}</span></td>
                    <td>
                        @if($u->is_active)<span class="badge-pill badge-ok">Aktif</span>
                        @else<span class="badge-pill badge-danger">Tidak Aktif</span>@endif
                    </td>
                    <td style="white-space:nowrap">
                        <button class="btn btn-sm btn-secondary" onclick="openEditUser({{ $u->id }})"><x-icon name="pencil" :size="13" /> Edit</button>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Padam pengguna {{ $u->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Padam</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div class="modal-overlay" id="addUserModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="users" :size="18" /> Tambah Pengguna Baharu</div>
                <div class="modal-close" onclick="closeModal('addUserModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Nama Penuh *</label><input name="name" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">No. Pekerja</label><input name="employee_no" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Emel *</label><input name="email" class="form-control" type="email" required></div>
                    <div class="form-group"><label class="form-label">Kata Laluan *</label><input name="password" class="form-control" type="password" required minlength="6"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jabatan</label>
                        <select name="department" class="form-control">
                            <option value="">Pilih...</option>
                            @foreach(['IT','Operasi','Pemasaran','Kewangan','HR','Pengurusan','Pentadbiran'] as $d)<option>{{ $d }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Jawatan</label><input name="position" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">No. Telefon</label><input name="phone" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Peranan *</label>
                        <select name="role" class="form-control" required>
                            @foreach($roles as $r)<option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editUserModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="editUserTitle"><x-icon name="pencil" :size="17" /> Kemaskini Pengguna</div>
                <div class="modal-close" onclick="closeModal('editUserModal')">✕</div>
            </div>
            <form method="POST" id="editUserForm">
                @csrf @method('PUT')
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Nama Penuh *</label><input name="name" id="eu_name" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">No. Pekerja</label><input name="employee_no" id="eu_employee_no" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Emel *</label><input name="email" id="eu_email" class="form-control" type="email" required></div>
                    <div class="form-group"><label class="form-label">Kata Laluan Baharu</label><input name="password" class="form-control" type="password" minlength="6" placeholder="Kosongkan jika tidak tukar"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jabatan</label>
                        <select name="department" id="eu_department" class="form-control">
                            <option value="">Pilih...</option>
                            @foreach(['IT','Operasi','Pemasaran','Kewangan','HR','Pengurusan','Pentadbiran'] as $d)<option>{{ $d }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Jawatan</label><input name="position" id="eu_position" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">No. Telefon</label><input name="phone" id="eu_phone" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Peranan *</label>
                        <select name="role" id="eu_role" class="form-control" required>
                            @foreach($roles as $r)<option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px">
                        <input type="checkbox" name="is_active" id="eu_active" value="1"> Pengguna Aktif
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kemaskini</button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any())
    <script>document.getElementById('addUserModal').classList.add('open');</script>
    @endif

    <script>
    const usersData = @json($users->keyBy('id'));

    function openEditUser(id) {
        const u = usersData[id];
        if (!u) return;
        document.getElementById('editUserTitle').textContent = u.name;
        document.getElementById('editUserForm').action = '/users/' + id;
        document.getElementById('eu_name').value = u.name || '';
        document.getElementById('eu_email').value = u.email || '';
        document.getElementById('eu_employee_no').value = u.employee_no || '';
        document.getElementById('eu_department').value = u.department || '';
        document.getElementById('eu_position').value = u.position || '';
        document.getElementById('eu_phone').value = u.phone || '';
        document.getElementById('eu_role').value = u.roles?.[0]?.name || 'staff';
        document.getElementById('eu_active').checked = u.is_active;
        document.getElementById('editUserModal').classList.add('open');
    }

    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
