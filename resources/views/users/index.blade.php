<x-fleet-layout title="Pengurusan Pengguna">
    <div class="page-header">
        <h2>Pengurusan Pengguna</h2>
        <p>Urus hak akses pengguna sistem</p>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>Nama</th><th>Jawatan</th><th>Jabatan</th><th>Peranan</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="avatar" style="width:28px;height:28px;font-size:10px">{{ $u->avatar_initials }}</div>
                            <div>
                                <div style="font-weight:600">{{ $u->name }}</div>
                                <div style="font-size:11px;color:var(--c-muted)">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $u->position ?? '—' }}</td>
                    <td>{{ $u->department ?? '—' }}</td>
                    <td>
                        @foreach($u->roles as $role)
                            @if($role->name === 'admin')<span class="badge-pill badge-purple">Admin</span>
                            @elseif($role->name === 'fleet')<span class="badge-pill badge-info">Fleet</span>
                            @elseif($role->name === 'guard')<span class="badge-pill badge-warn">Penjaga</span>
                            @else<span class="badge-pill badge-neutral">Staff</span>@endif
                        @endforeach
                    </td>
                    <td><span class="badge-pill badge-ok">Aktif</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-fleet-layout>
