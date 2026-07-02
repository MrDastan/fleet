<x-fleet-layout title="Pengurusan Saman">
    <div class="page-header">
        <h1>Pengurusan Saman</h1>
        <p>Saman trafik & kompaun kenderaan syarikat</p>
    </div>

    @if($unpaidCount > 0)
    <div class="alert alert-danger">
        <x-icon name="triangle-alert" :size="16" />
        <div><strong>{{ $unpaidCount }} saman belum dijelaskan.</strong> Jumlah tertunggak: <strong>RM {{ number_format($unpaidTotal) }}</strong>. Saman melebihi 30 hari boleh dikenakan denda tambahan.</div>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px">
        <a href="{{ route('saman.index', ['status' => 'belum_bayar']) }}" class="stat-card" style="text-decoration:none">
            <div class="stat-label">Belum Bayar</div>
            <div class="stat-val" style="color:var(--danger-text);margin-top:8px">{{ $unpaidCount }}</div>
            <div class="stat-sub red">RM {{ number_format($unpaidTotal) }} tertunggak</div>
        </a>
        <a href="{{ route('saman.index', ['status' => 'dalam_rayuan']) }}" class="stat-card" style="text-decoration:none">
            <div class="stat-label">Dalam Rayuan</div>
            <div class="stat-val" style="color:var(--warn-text);margin-top:8px">{{ $appealCount }}</div>
            <div class="stat-sub orange">RM {{ number_format($appealTotal) }} dalam semakan</div>
        </a>
        <a href="{{ route('saman.index', ['status' => 'telah_bayar']) }}" class="stat-card" style="text-decoration:none">
            <div class="stat-label">Telah Dijelaskan</div>
            <div class="stat-val" style="color:var(--ok);margin-top:8px">{{ $paidCount }}</div>
            <div class="stat-sub green">RM {{ number_format($paidTotal) }} tahun ini</div>
        </a>
        <a href="{{ route('saman.index') }}" class="stat-card" style="text-decoration:none">
            <div class="stat-label">Jumlah Saman</div>
            <div class="stat-val" style="margin-top:8px">{{ $totalAll }}</div>
            <div class="stat-sub" style="color:var(--muted)">RM {{ number_format($totalAmount) }} jumlah</div>
        </a>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('saman.index') }}" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
            <input type="text" name="search" class="form-control search-input" placeholder="Cari no. plat, no. saman..." value="{{ request('search') }}">
            <select name="status" class="form-control" style="width:150px" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="dalam_rayuan" {{ request('status') === 'dalam_rayuan' ? 'selected' : '' }}>Dalam Rayuan</option>
                <option value="telah_bayar" {{ request('status') === 'telah_bayar' ? 'selected' : '' }}>Telah Bayar</option>
            </select>
            <select name="vehicle_id" class="form-control" style="width:180px" onchange="this.form.submit()">
                <option value="">Semua Kenderaan</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plat }}</option>
                @endforeach
            </select>
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addSamanModal').classList.add('open')"><x-icon name="plus" :size="16" /> Rekod Saman</button>
    </div>

    <div class="card mb20">
        <table class="fleet-table">
            <thead>
                <tr><th>No. Saman</th><th>No. Plat</th><th>Pemandu</th><th>Jenis</th><th>Kesalahan</th><th>Tarikh</th><th>Lokasi</th><th>Jumlah</th><th>Status</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr style="{{ $r->status === 'telah_bayar' ? 'opacity:0.65' : '' }}">
                    <td><strong style="font-family:monospace;font-size:12px">{{ $r->saman_no }}</strong></td>
                    <td><strong>{{ $r->vehicle->plat }}</strong><div style="font-size:11px;color:var(--c-muted)">{{ $r->vehicle->model }}</div></td>
                    <td>{{ $r->driver?->name ?? '—' }}</td>
                    <td><span class="badge-pill {{ str_contains($r->saman_type, 'JPJ') ? 'badge-danger' : (str_contains($r->saman_type, 'AES') ? 'badge-info' : 'badge-warn') }}">{{ $r->saman_type }}</span></td>
                    <td>{{ $r->offense }}<div style="font-size:11px;color:var(--c-muted)">{{ $r->offense_detail }}</div></td>
                    <td>{{ $r->date->format('d M Y') }}</td>
                    <td>{{ $r->location }}<div style="font-size:11px;color:var(--c-muted)">{{ $r->location_detail }}</div></td>
                    <td><strong>RM {{ number_format($r->amount, 0) }}</strong></td>
                    <td>
                        @if($r->status === 'belum_bayar')<span class="badge-pill badge-danger">Belum Bayar</span>
                        @elseif($r->status === 'dalam_rayuan')<span class="badge-pill badge-warn">Dalam Rayuan</span>
                        @else<span class="badge-pill badge-ok">Telah Bayar</span>@endif
                    </td>
                    <td style="white-space:nowrap">
                        <button class="btn btn-sm btn-secondary" onclick="openSamanDetail({{ $r->id }})">Detail</button>
                        @if($r->status !== 'telah_bayar')
                            <button class="btn btn-sm btn-primary" onclick="openSamanUpdate({{ $r->id }}, '{{ $r->saman_no }}', '{{ $r->status }}')">Bayar</button>
                        @endif
                        @if($r->files->count())
                            <span style="font-size:11px;color:var(--muted)" title="{{ $r->files->count() }} fail">{{ $r->files->count() }} fail</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod saman</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary by vehicle -->
    @if($byVehicle->count())
    <div class="card">
        <div class="card-header"><span class="card-title"><span class="icon-accent"><x-icon name="bar-chart-3" :size="17" /></span>Saman Mengikut Kenderaan</span></div>
        <div class="card-body">
            @foreach($byVehicle as $bv)
                @php $pct = $totalAmount > 0 ? ($bv->total / $totalAmount) * 100 : 0; @endphp
                <div style="margin-bottom:12px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:13px;font-weight:600">{{ $bv->vehicle->plat }} — {{ $bv->vehicle->model }}</span>
                        <span style="font-size:13px;font-weight:700;color:{{ $pct > 40 ? 'var(--c-danger)' : ($pct > 25 ? 'var(--c-warn)' : 'var(--c-ok)') }}">
                            RM {{ number_format($bv->total) }} <span style="font-size:11px;font-weight:400;color:var(--c-muted)">({{ $bv->cnt }} saman)</span>
                        </span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill {{ $pct > 40 ? 'fill-danger' : ($pct > 25 ? 'fill-warn' : 'fill-ok') }}" style="width:{{ $pct }}%"></div></div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Add Saman Modal -->
    <div class="modal-overlay" id="addSamanModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="triangle-alert" :size="18" /> Rekod Saman Baharu</div>
                <div class="modal-close" onclick="closeModal('addSamanModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('saman.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Kenderaan *</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">Pilih...</option>
                            @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">No. Saman *</label>
                        <input name="saman_no" class="form-control" placeholder="JPJ-2026-009999" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jenis Saman *</label>
                        <select name="saman_type" class="form-control" required>
                            <option value="">Pilih jenis...</option>
                            <option>JPJ Trafik</option><option>Parkir DBKL</option><option>Parkir MBPJ</option>
                            <option>AES Kamera</option><option>Lain-lain</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Kesalahan *</label>
                        <input name="offense" class="form-control" placeholder="Jenis kesalahan" required>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Perincian Kesalahan</label>
                    <input name="offense_detail" class="form-control" placeholder="Keterangan tambahan">
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Tarikh *</label><input name="date" class="form-control" type="date" required value="{{ date('Y-m-d') }}"></div>
                    <div class="form-group"><label class="form-label">Masa</label><input name="time" class="form-control" type="time"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Lokasi *</label><input name="location" class="form-control" placeholder="Nama jalan / lebuh raya" required></div>
                    <div class="form-group"><label class="form-label">Kawasan</label><input name="location_detail" class="form-control" placeholder="Bandar / negeri"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jumlah Denda (RM) *</label><input name="amount" class="form-control" type="number" step="0.01" required></div>
                    <div class="form-group"><label class="form-label">Tarikh Akhir Bayar</label><input name="due_date" class="form-control" type="date"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Status</label>
                        <select name="status" class="form-control"><option value="belum_bayar">Belum Bayar</option><option value="dalam_rayuan">Dalam Rayuan</option><option value="telah_bayar">Telah Bayar</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">Tanggungjawab</label>
                        <select name="responsibility" class="form-control"><option>Pekerja (ditolak gaji)</option><option>Syarikat (dalam tugas)</option><option>Dalam siasatan</option></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addSamanModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Rekod</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal-overlay" id="samanUpdateModal">
        <div class="modal" style="max-width:420px">
            <div class="modal-header">
                <div class="modal-title" id="samanUpdateTitle">Kemaskini Status Saman</div>
                <div class="modal-close" onclick="closeModal('samanUpdateModal')">✕</div>
            </div>
            <form method="POST" id="samanUpdateForm">
                @csrf
                @method('PUT')
                <div class="form-group"><label class="form-label">Status Baharu *</label>
                    <select name="status" id="samanUpdateStatus" class="form-control">
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="dalam_rayuan">Dalam Rayuan</option>
                        <option value="telah_bayar">Telah Bayar</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Tarikh Bayaran</label>
                    <input name="payment_date" class="form-control" type="date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group"><label class="form-label">No. Resit</label>
                    <input name="receipt_no" class="form-control" placeholder="No. resit pembayaran">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('samanUpdateModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Saman Detail Modal -->
    <div class="modal-overlay" id="samanDetailModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="sdTitle">Detail Saman</div>
                <div class="modal-close" onclick="closeModal('samanDetailModal')">✕</div>
            </div>
            <div id="sdBody"></div>
            <div id="sdFiles"></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('samanDetailModal')">Tutup</button></div>
        </div>
    </div>

    @foreach($records as $r)
    <div id="saman-files-{{ $r->id }}" style="display:none">
        <x-file-upload type="saman" :id="$r->id" :files="$r->files" />
    </div>
    @endforeach

    <script>
    const samanData = @json($records->keyBy('id'));

    function openSamanDetail(id) {
        const s = samanData[id];
        if (!s) return;
        const v = s.vehicle;
        const statusMap = {belum_bayar:'Belum Bayar', dalam_rayuan:'Dalam Rayuan', telah_bayar:'Telah Bayar'};
        const statusBadge = s.status === 'telah_bayar' ? '<span class="badge-pill badge-ok">Telah Bayar</span>'
            : s.status === 'dalam_rayuan' ? '<span class="badge-pill badge-warn">Dalam Rayuan</span>'
            : '<span class="badge-pill badge-danger">Belum Bayar</span>';

        document.getElementById('sdTitle').textContent = s.saman_no;
        document.getElementById('sdBody').innerHTML = `
            <div class="detail-row"><div class="detail-label">No. Saman</div><div class="detail-val"><strong style="font-family:monospace">${s.saman_no}</strong></div></div>
            <div class="detail-row"><div class="detail-label">Jenis</div><div class="detail-val">${s.saman_type}</div></div>
            <div class="detail-row"><div class="detail-label">Kesalahan</div><div class="detail-val">${s.offense}${s.offense_detail ? ' — ' + s.offense_detail : ''}</div></div>
            <div class="detail-row"><div class="detail-label">Tarikh</div><div class="detail-val">${new Date(s.date).toLocaleDateString('ms-MY',{day:'numeric',month:'long',year:'numeric'})}${s.time ? ', ' + s.time : ''}</div></div>
            <div class="detail-row"><div class="detail-label">Lokasi</div><div class="detail-val">${s.location}${s.location_detail ? ', ' + s.location_detail : ''}</div></div>
            <div class="detail-row"><div class="detail-label">Kenderaan</div><div class="detail-val"><strong>${v.plat}</strong> — ${v.model}</div></div>
            <div class="detail-row"><div class="detail-label">Pemandu</div><div class="detail-val">${s.driver?.name || '—'}</div></div>
            <div class="detail-row"><div class="detail-label">Jumlah Denda</div><div class="detail-val"><strong style="color:var(--c-danger);font-size:16px">RM ${parseFloat(s.amount).toFixed(2)}</strong></div></div>
            <div class="detail-row"><div class="detail-label">Tanggungjawab</div><div class="detail-val">${s.responsibility || '—'}</div></div>
            <div class="detail-row"><div class="detail-label">Status</div><div class="detail-val">${statusBadge}</div></div>
            ${s.receipt_no ? '<div class="detail-row"><div class="detail-label">No. Resit</div><div class="detail-val">' + s.receipt_no + '</div></div>' : ''}
        `;

        const filesEl = document.getElementById('saman-files-' + id);
        document.getElementById('sdFiles').innerHTML = filesEl ? filesEl.innerHTML : '';

        document.getElementById('samanDetailModal').classList.add('open');
    }

    function openSamanUpdate(id, no, status) {
        document.getElementById('samanUpdateTitle').textContent = 'Kemaskini — ' + no;
        document.getElementById('samanUpdateForm').action = '/saman/' + id;
        document.getElementById('samanUpdateStatus').value = status;
        document.getElementById('samanUpdateModal').classList.add('open');
    }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
