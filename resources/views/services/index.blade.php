<x-fleet-layout title="Servis & Penyelenggaraan">
    <div class="page-header">
        <h2>Pengurusan Servis</h2>
        <p>Rekod servis, penyelenggaraan dan pembaikan kenderaan</p>
    </div>

    <div class="search-bar">
        <input type="text" class="form-control search-input" placeholder="🔍  Cari rekod servis...">
        <button class="btn btn-primary" onclick="document.getElementById('addServisModal').classList.add('open')">+ Rekod Baharu</button>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead>
                <tr><th>No. Plat</th><th>Jenis Servis</th><th>Tarikh</th><th>Bengkel</th><th>Km</th><th>Kos</th><th>Status</th></tr>
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
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod servis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add Servis Modal -->
    <div class="modal-overlay" id="addServisModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">🔧 Rekod Servis Baharu</div>
                <div class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</div>
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
                            <option>Tukar Tayar</option><option>Tukar Brek</option><option>Pembaikan</option><option>Lain-lain</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Tarikh *</label><input name="date" class="form-control" type="date" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Bengkel</label><input name="workshop" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Odometer (km)</label><input name="odometer_km" class="form-control" type="number"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Kos (RM)</label><input name="cost" class="form-control" type="number" step="0.01"></div>
                    <div class="form-group"><label class="form-label">Status</label>
                        <select name="status" class="form-control"><option value="dijadual">Dijadual</option><option value="dalam_proses">Dalam Proses</option><option value="selesai">Selesai</option></select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Rekod</button>
                </div>
            </form>
        </div>
    </div>
</x-fleet-layout>
