<x-fleet-layout title="Servis & Penyelenggaraan">
    <div class="page-header">
        <h2>Pengurusan Servis</h2>
        <p>Rekod servis, penyelenggaraan dan pembaikan kenderaan</p>
    </div>

    <div class="tabs">
        <a href="{{ route('services.index') }}" class="tab {{ !request('status') ? 'active' : '' }}">Semua ({{ $counts['all'] }})</a>
        <a href="{{ route('services.index', ['status' => 'dalam_proses']) }}" class="tab {{ request('status') === 'dalam_proses' ? 'active' : '' }}">Dalam Proses ({{ $counts['dalam_proses'] }})</a>
        <a href="{{ route('services.index', ['status' => 'selesai']) }}" class="tab {{ request('status') === 'selesai' ? 'active' : '' }}">Selesai ({{ $counts['selesai'] }})</a>
        <a href="{{ route('services.index', ['status' => 'dijadual']) }}" class="tab {{ request('status') === 'dijadual' ? 'active' : '' }}">Dijadual ({{ $counts['dijadual'] }})</a>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('services.index') }}" style="display:flex;gap:10px;flex:1">
            <input type="text" name="search" class="form-control search-input" placeholder="🔍  Cari rekod servis..." value="{{ request('search') }}">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addServisModal').classList.add('open')">+ Rekod Baharu</button>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead>
                <tr><th>No. Plat</th><th>Jenis Servis</th><th>Tarikh</th><th>Bengkel</th><th>Km</th><th>Kos</th><th>Status</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td><strong>{{ $r->vehicle->plat }}</strong><div style="font-size:11px;color:var(--c-muted)">{{ $r->vehicle->model }}</div></td>
                    <td>{{ $r->service_type }}</td>
                    <td>{{ $r->date->format('d M Y') }}</td>
                    <td>{{ $r->workshop ?? '—' }}</td>
                    <td>{{ $r->odometer_km ? number_format($r->odometer_km) . ' km' : '—' }}</td>
                    <td>{{ $r->cost ? 'RM ' . number_format($r->cost, 2) : '—' }}</td>
                    <td>
                        @if($r->status === 'selesai')<span class="badge-pill badge-ok">Selesai</span>
                        @elseif($r->status === 'dalam_proses')<span class="badge-pill badge-warn">Dalam Proses</span>
                        @else<span class="badge-pill badge-info">Dijadual</span>@endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="openServiceDetail({{ $r->id }})">Detail</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod servis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add Service Modal -->
    <div class="modal-overlay" id="addServisModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">🔧 Rekod Servis Baharu</div>
                <div class="modal-close" onclick="closeModal('addServisModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('services.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Kenderaan *</label>
                    <select name="vehicle_id" class="form-control" required>
                        <option value="">Pilih kenderaan...</option>
                        @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>@endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jenis Servis *</label>
                        <select name="service_type" class="form-control" required>
                            <option value="">Pilih jenis...</option>
                            <option>Servis Minor (5,000km)</option><option>Servis Major (10,000km)</option>
                            <option>Tukar Tayar</option><option>Tukar Brek</option><option>Tukar Minyak Hitam</option><option>Pembaikan</option><option>Lain-lain</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Tarikh *</label><input name="date" class="form-control" type="date" required value="{{ date('Y-m-d') }}"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Bengkel</label><input name="workshop" class="form-control" placeholder="Nama bengkel"></div>
                    <div class="form-group"><label class="form-label">Odometer (km)</label><input name="odometer_km" class="form-control" type="number"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Kos (RM)</label><input name="cost" class="form-control" type="number" step="0.01" placeholder="0.00"></div>
                    <div class="form-group"><label class="form-label">Status</label>
                        <select name="status" class="form-control"><option value="dijadual">Dijadual</option><option value="dalam_proses">Dalam Proses</option><option value="selesai">Selesai</option></select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2" placeholder="Butiran kerja servis..."></textarea></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addServisModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Rekod</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Service Detail Modal -->
    <div class="modal-overlay" id="serviceDetailModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="svcDetailTitle">🔧 Detail Servis</div>
                <div class="modal-close" onclick="closeModal('serviceDetailModal')">✕</div>
            </div>
            <div id="svcDetailBody"></div>
            <div id="svcDetailFiles"></div>
            <form method="POST" id="svcUpdateForm" style="margin-top:16px">
                @csrf
                @method('PUT')
                <div style="font-size:12px;font-weight:600;color:var(--c-muted);margin-bottom:8px">KEMASKINI STATUS</div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Status</label>
                        <select name="status" id="svcEditStatus" class="form-control">
                            <option value="dijadual">Dijadual</option><option value="dalam_proses">Dalam Proses</option><option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Kos (RM)</label><input name="cost" id="svcEditCost" class="form-control" type="number" step="0.01"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('serviceDetailModal')">Tutup</button>
                    <button type="submit" class="btn btn-primary">Kemaskini</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($records as $r)
    <div id="svc-files-{{ $r->id }}" style="display:none">
        <x-file-upload type="service" :id="$r->id" :files="$r->files" />
    </div>
    @endforeach

    <script>
    const servicesData = @json($records->keyBy('id'));

    function openServiceDetail(id) {
        const s = servicesData[id];
        if (!s) return;
        const v = s.vehicle;
        const statusBadge = s.status === 'selesai' ? '<span class="badge-pill badge-ok">Selesai</span>'
            : s.status === 'dalam_proses' ? '<span class="badge-pill badge-warn">Dalam Proses</span>'
            : '<span class="badge-pill badge-info">Dijadual</span>';

        document.getElementById('svcDetailTitle').innerHTML = '🔧 Detail Servis — ' + v.plat;
        document.getElementById('svcDetailBody').innerHTML = `
            <div class="detail-row"><div class="detail-label">No. Plat</div><div class="detail-val"><strong>${v.plat}</strong></div></div>
            <div class="detail-row"><div class="detail-label">Model</div><div class="detail-val">${v.model} ${v.year || ''}</div></div>
            <div class="detail-row"><div class="detail-label">Jenis Servis</div><div class="detail-val">${s.service_type}</div></div>
            <div class="detail-row"><div class="detail-label">Tarikh</div><div class="detail-val">${new Date(s.date).toLocaleDateString('ms-MY', {day:'numeric',month:'long',year:'numeric'})}</div></div>
            <div class="detail-row"><div class="detail-label">Bengkel</div><div class="detail-val">${s.workshop || '—'}</div></div>
            <div class="detail-row"><div class="detail-label">Odometer</div><div class="detail-val">${s.odometer_km ? s.odometer_km.toLocaleString() + ' km' : '—'}</div></div>
            <div class="detail-row"><div class="detail-label">Kos</div><div class="detail-val"><strong>${s.cost ? 'RM ' + parseFloat(s.cost).toFixed(2) : '—'}</strong></div></div>
            <div class="detail-row"><div class="detail-label">Status</div><div class="detail-val">${statusBadge}</div></div>
            ${s.notes ? '<div class="detail-row"><div class="detail-label">Catatan</div><div class="detail-val">' + s.notes + '</div></div>' : ''}
            ${s.items ? '<div style="margin-top:12px;font-size:12px;font-weight:600;color:var(--c-muted)">KERJA SERVIS</div>' + s.items.map(i => '<div style="padding:4px 0;font-size:13px">• ' + i + '</div>').join('') : ''}
        `;

        const filesEl = document.getElementById('svc-files-' + id);
        document.getElementById('svcDetailFiles').innerHTML = filesEl ? filesEl.innerHTML : '';

        document.getElementById('svcUpdateForm').action = '/services/' + id;
        document.getElementById('svcEditStatus').value = s.status;
        document.getElementById('svcEditCost').value = s.cost || '';
        document.getElementById('serviceDetailModal').classList.add('open');
    }

    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
    });
    </script>
</x-fleet-layout>
