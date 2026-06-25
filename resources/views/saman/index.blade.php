<x-fleet-layout title="Pengurusan Saman">
    <div class="page-header">
        <h2>Pengurusan Saman</h2>
        <p>Rekod dan pemantauan saman trafik, parkir & kesalahan lain bagi semua kenderaan syarikat</p>
    </div>

    @if($unpaidCount > 0)
    <div class="alert alert-danger">
        <span>🚨</span>
        <div><strong>{{ $unpaidCount }} saman belum dijelaskan.</strong> Jumlah tertunggak: <strong>RM {{ number_format($unpaidTotal) }}</strong>. Saman melebihi 30 hari boleh dikenakan denda tambahan.</div>
    </div>
    @endif

    <div class="stats-grid" style="margin-bottom:20px">
        <a href="{{ route('saman.index', ['status' => 'belum_bayar']) }}" class="stat-card" style="border-top:3px solid var(--c-danger);text-decoration:none">
            <div class="stat-icon" style="background:#ffe8e8">🚨</div>
            <div class="stat-val" style="color:var(--c-danger)">{{ $unpaidCount }}</div>
            <div class="stat-label">Belum Dijelaskan</div>
            <div class="stat-sub red">RM {{ number_format($unpaidTotal) }} tertunggak</div>
        </a>
        <a href="{{ route('saman.index', ['status' => 'dalam_rayuan']) }}" class="stat-card" style="border-top:3px solid var(--c-warn);text-decoration:none">
            <div class="stat-icon" style="background:#fff3e0">⏳</div>
            <div class="stat-val" style="color:var(--c-warn)">{{ $appealCount }}</div>
            <div class="stat-label">Dalam Rayuan</div>
            <div class="stat-sub orange">RM {{ number_format($appealTotal) }} dalam semakan</div>
        </a>
        <a href="{{ route('saman.index', ['status' => 'telah_bayar']) }}" class="stat-card" style="border-top:3px solid var(--c-ok);text-decoration:none">
            <div class="stat-icon" style="background:#e8fff6">✅</div>
            <div class="stat-val" style="color:var(--c-ok)">{{ $paidCount }}</div>
            <div class="stat-label">Telah Dijelaskan</div>
            <div class="stat-sub green">RM {{ number_format($paidTotal) }} tahun ini</div>
        </a>
        <a href="{{ route('saman.index') }}" class="stat-card" style="border-top:3px solid var(--c-sky);text-decoration:none">
            <div class="stat-icon" style="background:#fff0e8">📋</div>
            <div class="stat-val">{{ $totalAll }}</div>
            <div class="stat-label">Jumlah Saman</div>
            <div class="stat-sub" style="color:var(--c-muted)">RM {{ number_format($totalAmount) }} jumlah</div>
        </a>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('saman.index') }}" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
            <input type="text" name="search" class="form-control search-input" placeholder="🔍  Cari no. plat, no. saman..." value="{{ request('search') }}">
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
        <button class="btn btn-primary" onclick="document.getElementById('addSamanModal').classList.add('open')">+ Rekod Saman</button>
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
                    <td>
                        @if($r->status !== 'telah_bayar')
                            <button class="btn btn-sm btn-primary" onclick="openSamanUpdate({{ $r->id }}, '{{ $r->saman_no }}', '{{ $r->status }}')">Kemaskini</button>
                        @else
                            <span style="font-size:11px;color:var(--c-muted)">{{ $r->payment_date?->format('d/m/Y') }}</span>
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
        <div class="card-header"><span class="card-title">📊 Saman Mengikut Kenderaan</span></div>
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
                <div class="modal-title">🚨 Rekod Saman Baharu</div>
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

    <script>
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
