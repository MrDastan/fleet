<x-fleet-layout title="Permohonan Kenderaan">
    <div class="page-header">
        <h1>Permohonan Kenderaan</h1>
        <p>Sistem kelulusan 3 peringkat: Staff Mohon → Penjaga Sahkan → Fleet Luluskan</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="margin-bottom:20px">
        <a href="{{ route('approvals.index') }}" class="stat-card" style="text-decoration:none">
            <div class="stat-icon soft-accent"><x-icon name="clipboard-check" :size="18" /></div>
            <div class="stat-val">{{ $counts['all'] }}</div>
            <div class="stat-label">Jumlah Permohonan</div>
        </a>
        <a href="{{ route('approvals.index', ['status' => 'pending_guard']) }}" class="stat-card" style="text-decoration:none">
            <div class="stat-icon soft-warn"><x-icon name="clock" :size="18" /></div>
            <div class="stat-val">{{ $counts['pending_guard'] }}</div>
            <div class="stat-label">Menunggu Penjaga</div>
            @if($counts['pending_guard'] > 0)<div class="stat-sub orange">Perlu tindakan segera</div>@endif
        </a>
        <a href="{{ route('approvals.index', ['status' => 'pending_fleet']) }}" class="stat-card" style="text-decoration:none">
            <div class="stat-icon soft-info"><x-icon name="file-text" :size="18" /></div>
            <div class="stat-val">{{ $counts['pending_fleet'] }}</div>
            <div class="stat-label">Menunggu Fleet</div>
            @if($counts['pending_fleet'] > 0)<div class="stat-sub" style="color:var(--info)">Perlu semakan</div>@endif
        </a>
        <a href="{{ route('approvals.index', ['status' => 'approved']) }}" class="stat-card" style="text-decoration:none">
            <div class="stat-icon soft-ok"><x-icon name="check" :size="18" /></div>
            <div class="stat-val">{{ $counts['approved'] }}</div>
            <div class="stat-label">Diluluskan</div>
        </a>
    </div>

    <!-- Role-specific alert -->
    @if($role === 'guard' && $counts['pending_guard'] > 0)
        <div class="alert alert-warn">
            <div><strong>Penjaga:</strong> {{ $counts['pending_guard'] }} permohonan menunggu pengesahan anda. Sila semak ketersediaan dan kondisi kenderaan.</div>
        </div>
    @elseif($role === 'fleet' && $counts['pending_fleet'] > 0)
        <div class="alert alert-warn">
            <div><strong>Fleet:</strong> {{ $counts['pending_fleet'] }} permohonan telah disahkan penjaga. Sila nilai dan luluskan.</div>
        </div>
    @endif

    <!-- Tabs -->
    <div class="tabs">
        <a href="{{ route('approvals.index') }}" class="tab {{ !request('status') ? 'active' : '' }}">Semua ({{ $counts['all'] }})</a>
        <a href="{{ route('approvals.index', ['status' => 'pending_guard']) }}" class="tab {{ request('status') === 'pending_guard' ? 'active' : '' }}">Penjaga ({{ $counts['pending_guard'] }})</a>
        <a href="{{ route('approvals.index', ['status' => 'pending_fleet']) }}" class="tab {{ request('status') === 'pending_fleet' ? 'active' : '' }}">Fleet ({{ $counts['pending_fleet'] }})</a>
        <a href="{{ route('approvals.index', ['status' => 'approved']) }}" class="tab {{ request('status') === 'approved' ? 'active' : '' }}">Lulus ({{ $counts['approved'] }})</a>
        <a href="{{ route('approvals.index', ['status' => 'rejected']) }}" class="tab {{ request('status') === 'rejected' ? 'active' : '' }}">Tolak ({{ $counts['rejected'] }})</a>
        <a href="{{ route('approvals.index', ['status' => 'completed']) }}" class="tab {{ request('status') === 'completed' ? 'active' : '' }}">Selesai ({{ $counts['completed'] }})</a>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('approvals.index') }}" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
            <input type="text" name="search" class="form-control search-input" placeholder="Cari pemohon, no. plat..." value="{{ request('search') }}">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('newApprovalModal').classList.add('open')"><x-icon name="plus" :size="16" /> Mohon Kenderaan</button>
    </div>

    <!-- Table -->
    <div class="card mb20">
        <table class="fleet-table">
            <thead>
                <tr><th>No.</th><th>Pemohon</th><th>Kenderaan</th><th>Tarikh Guna</th><th>Tujuan</th><th>Peringkat</th><th>Status</th><th>Dihantar</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @forelse($requests as $a)
                @php
                    $stageLabels = [1 => 'Peringkat 1/3', 2 => 'Peringkat 2/3', 3 => 'Peringkat 3/3', 4 => 'Selesai'];
                    $statusBadges = [
                        'pending_guard' => '<span class="badge-pill badge-warn">Menunggu Penjaga</span>',
                        'pending_fleet' => '<span class="badge-pill badge-info">Menunggu Fleet</span>',
                        'approved' => '<span class="badge-pill badge-ok">Diluluskan</span>',
                        'rejected' => '<span class="badge-pill badge-danger">Ditolak</span>',
                        'completed' => '<span class="badge-pill badge-neutral">Selesai</span>',
                    ];
                    $canGuard = $role === 'guard' && $a->status === 'pending_guard';
                    $canFleet = in_array($role, ['fleet', 'admin']) && $a->status === 'pending_fleet';
                    $canOverride = $role === 'admin' && in_array($a->status, ['pending_guard', 'pending_fleet']);
                    $canComplete = in_array($role, ['admin', 'fleet']) && $a->status === 'approved';
                @endphp
                <tr>
                    <td><strong style="font-family:monospace;font-size:11px">{{ $a->request_no }}</strong></td>
                    <td><strong>{{ $a->requester->name }}</strong><div style="font-size:11px;color:var(--c-muted)">{{ $a->requester->department }}</div></td>
                    <td><strong>{{ $a->vehicle->plat }}</strong><div style="font-size:11px;color:var(--c-muted)">{{ $a->vehicle->model }}</div></td>
                    <td>{{ $a->use_date->format('d M Y') }}<div style="font-size:11px;color:var(--c-muted)">{{ $a->time_start }} – {{ $a->time_end }}</div></td>
                    <td>{{ $a->purpose }}<div style="font-size:11px;color:var(--c-muted)">{{ $a->destination }}</div></td>
                    <td><span style="font-size:11px;color:var(--c-muted)">{{ $stageLabels[$a->stage] ?? '' }}</span></td>
                    <td>{!! $statusBadges[$a->status] ?? $a->status !!}</td>
                    <td style="font-size:11px;color:var(--c-muted)">{{ $a->created_at->format('d M, H:i') }}</td>
                    <td style="white-space:nowrap">
                        <button class="btn btn-sm btn-secondary" onclick="viewDetail({{ $a->id }})">Detail</button>
                        @if($canGuard)
                            <button class="btn btn-sm btn-primary" onclick="openGuardModal({{ $a->id }})">Semak</button>
                        @endif
                        @if($canFleet)
                            <button class="btn btn-sm btn-primary" onclick="openFleetModal({{ $a->id }})">Nilai</button>
                        @endif
                        @if($canOverride)
                            <button class="btn btn-sm" style="background:#7c3aed;color:#fff;padding:8px;border-radius:8px;border:none;cursor:pointer" onclick="openOverrideModal({{ $a->id }})"><x-icon name="zap" :size="13" /></button>
                        @endif
                        @if($canComplete)
                            <form method="POST" action="{{ route('approvals.complete', $a) }}" style="display:inline">@csrf @method('PUT')
                                <button class="btn btn-sm btn-secondary" type="submit"><x-icon name="flag" :size="13" /></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:var(--c-muted);padding:24px">Tiada permohonan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Flow chart -->
    <div class="card">
        <div class="card-header"><span class="card-title"><span class="icon-accent"><x-icon name="route" :size="17" /></span>Carta Aliran Kelulusan</span></div>
        <div class="card-body">
            <div style="display:flex;align-items:flex-start;gap:0;overflow-x:auto;padding-bottom:8px">
                @foreach([
                    ['icon' => 'clipboard-check', 'kind' => 'accent', 'title' => 'Staff Mohon', 'sub' => 'Isi tarikh, tujuan & destinasi', 'badge' => 'Peringkat 1', 'badgeClass' => 'badge-neutral'],
                    ['icon' => 'key', 'kind' => 'ok', 'title' => 'Penjaga Sahkan', 'sub' => 'Semak kondisi & ketersediaan', 'badge' => 'Peringkat 2', 'badgeClass' => 'badge-ok'],
                    ['icon' => 'car', 'kind' => 'info', 'title' => 'Fleet Luluskan', 'sub' => 'Nilai keutamaan & polisi', 'badge' => 'Peringkat 3', 'badgeClass' => 'badge-info'],
                    ['icon' => 'check', 'kind' => 'ok', 'title' => 'Kenderaan Sedia', 'sub' => 'Ambil kunci, log keluar', 'badge' => 'Selesai', 'badgeClass' => 'badge-ok'],
                ] as $i => $step)
                    @if($i > 0)
                    <div style="flex:1;display:flex;align-items:center;padding-top:22px;min-width:30px">
                        <div style="flex:1;height:2px;background:var(--border)"></div>
                        <div style="font-size:10px;color:var(--muted);white-space:nowrap;padding:0 4px">→</div>
                        <div style="flex:1;height:2px;background:var(--border)"></div>
                    </div>
                    @endif
                    <div style="display:flex;flex-direction:column;align-items:center;min-width:120px">
                        <div class="soft-{{ $step['kind'] }}" style="width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center"><x-icon :name="$step['icon']" :size="19" /></div>
                        <div style="font-size:12px;font-weight:600;margin-top:6px;text-align:center">{{ $step['title'] }}</div>
                        <div style="font-size:11px;color:var(--muted);text-align:center;margin-top:2px">{{ $step['sub'] }}</div>
                        <span class="badge-pill {{ $step['badgeClass'] }}" style="margin-top:6px;font-size:10px">{{ $step['badge'] }}</span>
                    </div>
                @endforeach
            </div>
            @if($role === 'admin')
            <div style="margin-top:14px;padding:10px 14px;background:var(--bg);border-radius:10px;border-left:3px solid #7c3aed;font-size:12px;color:var(--muted)">
                <strong style="color:#5b21b6">Admin Override:</strong> Admin boleh lulus atau batal permohonan pada mana-mana peringkat tanpa menunggu giliran.
            </div>
            @endif
        </div>
    </div>

    <!-- New Approval Modal -->
    <div class="modal-overlay" id="newApprovalModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="clipboard-check" :size="18" /> Permohonan Kenderaan Baharu</div>
                <div class="modal-close" onclick="closeModal('newApprovalModal')">✕</div>
            </div>
            <div class="alert alert-info" style="margin-bottom:14px">
                <div>Permohonan akan melalui <strong>2 peringkat kelulusan</strong>: Penjaga (30 min) → Fleet (2 jam).</div>
            </div>
            <form method="POST" action="{{ route('approvals.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Kenderaan Diperlukan *</label>
                    <select name="vehicle_id" class="form-control" required>
                        <option value="">Pilih kenderaan...</option>
                        @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>@endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Tarikh Guna *</label><input name="use_date" class="form-control" type="date" required value="{{ date('Y-m-d') }}"></div>
                    <div class="form-group"><label class="form-label">Masa Mula — Jangka Pulang</label>
                        <div style="display:flex;gap:6px"><input name="time_start" class="form-control" type="time" required value="08:00" style="flex:1"><input name="time_end" class="form-control" type="time" required value="17:00" style="flex:1"></div>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Tujuan Perjalanan *</label>
                    <select name="purpose" class="form-control" required>
                        <option value="">Pilih tujuan...</option>
                        <option>Lawatan klien</option><option>Mesyuarat rasmi</option><option>Urusan bank / kewangan</option>
                        <option>Penghantaran dokumen</option><option>Site visit / inspection</option><option>Perjalanan rasmi lain</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Destinasi *</label><input name="destination" class="form-control" placeholder="Contoh: Pejabat Klien, Jalan Ampang KL" required></div>
                <div class="form-group"><label class="form-label">Penumpang (jika ada)</label><input name="passengers" class="form-control" placeholder="Nama penumpang"></div>
                <div class="form-group"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2" placeholder="Keperluan khas..."></textarea></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newApprovalModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Hantar Permohonan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Guard Action Modal -->
    <div class="modal-overlay" id="guardModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="key" :size="18" /> Tindakan Penjaga — Peringkat 2</div>
                <div class="modal-close" onclick="closeModal('guardModal')">✕</div>
            </div>
            <div id="guardInfo" style="background:var(--c-bg);border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px"></div>
            <form method="POST" id="guardForm">
                @csrf @method('PUT')
                <div style="font-size:12px;font-weight:600;color:var(--c-muted);margin-bottom:10px">SEMAKAN KENDERAAN</div>
                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px">
                    @foreach(['Kenderaan tersedia di stor','Kondisi kenderaan baik','Bahan api mencukupi','Dokumen lengkap (roadtax, insuran)'] as $i => $check)
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px">
                        <input type="checkbox" name="guard_checklist[]" value="{{ $check }}"> {{ $check }}
                    </label>
                    @endforeach
                </div>
                <div class="form-group"><label class="form-label">Odometer Semasa (km)</label><input name="guard_odometer" class="form-control" type="number" placeholder="Bacaan odometer"></div>
                <div class="form-group"><label class="form-label">Catatan Penjaga</label><textarea name="guard_note" class="form-control" rows="2" placeholder="Catatan kondisi..."></textarea></div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="reject" class="btn btn-danger">Tolak</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('guardModal')">Batal</button>
                    <button type="submit" name="action" value="approve" class="btn btn-primary">Sahkan & Hantar ke Fleet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fleet Action Modal -->
    <div class="modal-overlay" id="fleetModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="car" :size="18" /> Tindakan Fleet — Peringkat 3</div>
                <div class="modal-close" onclick="closeModal('fleetModal')">✕</div>
            </div>
            <div id="fleetInfo" style="background:var(--c-bg);border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px"></div>
            <div style="background:#E3F2EA;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:12px;border-left:3px solid var(--ok)">
                <strong style="color:var(--ok)">Penjaga telah sahkan:</strong> <span id="fleetGuardNote"></span>
            </div>
            <form method="POST" id="fleetForm">
                @csrf @method('PUT')
                <div class="form-group"><label class="form-label">Keutamaan</label>
                    <select name="fleet_priority" class="form-control">
                        <option>Biasa — perjalanan rutin</option>
                        <option>Sederhana — keperluan jabatan</option>
                        <option>Tinggi — keperluan segera / klien</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Syarat / Arahan Khas</label><textarea name="fleet_note" class="form-control" rows="2" placeholder="Contoh: Pulang sebelum 5pm..."></textarea></div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="reject" class="btn btn-danger">Tolak</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('fleetModal')">Batal</button>
                    <button type="submit" name="action" value="approve" class="btn btn-primary">Luluskan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Override Modal -->
    <div class="modal-overlay" id="overrideModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="zap" :size="18" /> Admin Override</div>
                <div class="modal-close" onclick="closeModal('overrideModal')">✕</div>
            </div>
            <div class="alert alert-warn"><x-icon name="triangle-alert" :size="16" /><div>Override akan <strong>melepasi semua peringkat</strong> kelulusan. Tindakan direkodkan.</div></div>
            <div id="overrideInfo" style="background:var(--c-bg);border-radius:8px;padding:12px;margin:14px 0;font-size:13px"></div>
            <form method="POST" id="overrideForm">
                @csrf @method('PUT')
                <div class="form-group"><label class="form-label">Tindakan *</label>
                    <select name="override_action" class="form-control">
                        <option value="approve">Lulus segera (skip semua peringkat)</option>
                        <option value="reject">Tolak permohonan</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Sebab Override * (wajib untuk audit)</label>
                    <textarea name="override_reason" class="form-control" rows="3" placeholder="Nyatakan sebab..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('overrideModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Sahkan Override</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal-overlay" id="detailModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="detailTitle">Detail Permohonan</div>
                <div class="modal-close" onclick="closeModal('detailModal')">✕</div>
            </div>
            <div id="detailBody"></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('detailModal')">Tutup</button></div>
        </div>
    </div>

    <script>
    const approvalData = @json($requests->keyBy('id'));

    function approvalSummary(a) {
        return `<strong>${a.requester.name}</strong> (${a.requester.department ?? '—'}) memohon
            <strong>${a.vehicle.plat}</strong> pada ${a.use_date.split('T')[0]}
            (${a.time_start}–${a.time_end}) untuk <em>${a.purpose}</em> ke ${a.destination}.`;
    }

    function openGuardModal(id) {
        const a = approvalData[id];
        document.getElementById('guardInfo').innerHTML = approvalSummary(a);
        document.getElementById('guardForm').action = '/approvals/' + id + '/guard';
        document.getElementById('guardModal').classList.add('open');
    }

    function openFleetModal(id) {
        const a = approvalData[id];
        document.getElementById('fleetInfo').innerHTML = approvalSummary(a);
        document.getElementById('fleetGuardNote').textContent = a.guard_note || 'Disahkan';
        document.getElementById('fleetForm').action = '/approvals/' + id + '/fleet';
        document.getElementById('fleetModal').classList.add('open');
    }

    function openOverrideModal(id) {
        const a = approvalData[id];
        document.getElementById('overrideInfo').innerHTML = approvalSummary(a);
        document.getElementById('overrideForm').action = '/approvals/' + id + '/override';
        document.getElementById('overrideModal').classList.add('open');
    }

    function viewDetail(id) {
        const a = approvalData[id];
        const statusMap = {
            pending_guard: 'Menunggu Penjaga',
            pending_fleet: 'Menunggu Fleet',
            approved: 'Diluluskan',
            rejected: 'Ditolak',
            completed: 'Selesai'
        };
        let html = `
            <div class="detail-row"><div class="detail-label">No. Permohonan</div><div class="detail-val"><strong style="font-family:monospace">${a.request_no}</strong></div></div>
            <div class="detail-row"><div class="detail-label">Pemohon</div><div class="detail-val">${a.requester.name} (${a.requester.department || '—'})</div></div>
            <div class="detail-row"><div class="detail-label">Kenderaan</div><div class="detail-val">${a.vehicle.plat} — ${a.vehicle.model}</div></div>
            <div class="detail-row"><div class="detail-label">Tarikh</div><div class="detail-val">${a.use_date.split('T')[0]}</div></div>
            <div class="detail-row"><div class="detail-label">Masa</div><div class="detail-val">${a.time_start} – ${a.time_end}</div></div>
            <div class="detail-row"><div class="detail-label">Tujuan</div><div class="detail-val">${a.purpose}</div></div>
            <div class="detail-row"><div class="detail-label">Destinasi</div><div class="detail-val">${a.destination}</div></div>
            <div class="detail-row"><div class="detail-label">Status</div><div class="detail-val">${statusMap[a.status] || a.status}</div></div>
        `;
        if (a.guard_note) html += `<div class="detail-row"><div class="detail-label">Catatan Penjaga</div><div class="detail-val">${a.guard_note}</div></div>`;
        if (a.fleet_note) html += `<div class="detail-row"><div class="detail-label">Catatan Fleet</div><div class="detail-val">${a.fleet_note}</div></div>`;
        if (a.fleet_priority) html += `<div class="detail-row"><div class="detail-label">Keutamaan</div><div class="detail-val">${a.fleet_priority}</div></div>`;
        if (a.admin_override_reason) html += `<div class="detail-row"><div class="detail-label">Override</div><div class="detail-val">${a.admin_override_reason}</div></div>`;
        if (a.passengers) html += `<div class="detail-row"><div class="detail-label">Penumpang</div><div class="detail-val">${a.passengers}</div></div>`;
        if (a.notes) html += `<div class="detail-row"><div class="detail-label">Catatan</div><div class="detail-val">${a.notes}</div></div>`;

        document.getElementById('detailTitle').textContent = 'Detail — ' + a.request_no;
        document.getElementById('detailBody').innerHTML = html;
        document.getElementById('detailModal').classList.add('open');
    }

    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
