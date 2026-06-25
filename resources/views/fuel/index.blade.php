<x-fleet-layout title="Rekod Bahan Api">
    <div class="page-header">
        <h2>Rekod Bahan Api</h2>
        <p>Log pengisian bahan api dan analisis penggunaan</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff8e0">⛽</div>
            <div class="stat-val">{{ number_format($totalLiters, 0) }}L</div>
            <div class="stat-label">Jumlah {{ now()->translatedFormat('M Y') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fb">💳</div>
            <div class="stat-val">RM {{ number_format($totalCost, 0) }}</div>
            <div class="stat-label">Kos Bahan Api</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8fff6">📏</div>
            <div class="stat-val">{{ $avgConsumption ? number_format($avgConsumption, 1) : '—' }}</div>
            <div class="stat-label">L/100km Purata</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8e8">🚨</div>
            <div class="stat-val">{{ $highUsage }}</div>
            <div class="stat-label">Penggunaan Tinggi</div>
            @if($highUsage > 0)<div class="stat-sub red">Melebihi 10 L/100km</div>@endif
        </div>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('fuel.index') }}" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
            <select name="vehicle_id" class="form-control" style="width:200px" onchange="this.form.submit()">
                <option value="">Semua Kenderaan</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plat }} — {{ $v->model }}</option>
                @endforeach
            </select>
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addFuelModal').classList.add('open')">+ Log Pengisian</button>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>Tarikh & Masa</th><th>No. Plat</th><th>Pemandu</th><th>Stesen</th><th>Liter</th><th>Harga/L</th><th>Jumlah</th><th>Odometer</th><th>L/100km</th><th></th></tr></thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td>{{ $r->datetime->format('d M, H:i') }}</td>
                    <td><strong>{{ $r->vehicle->plat }}</strong></td>
                    <td>{{ $r->driver?->name ?? '—' }}</td>
                    <td>{{ $r->station ?? '—' }}</td>
                    <td>{{ number_format($r->liters, 0) }}L</td>
                    <td>RM {{ number_format($r->price_per_liter, 2) }}</td>
                    <td><strong>RM {{ number_format($r->total_cost, 2) }}</strong></td>
                    <td>{{ number_format($r->odometer_km) }} km</td>
                    <td>
                        @if($r->consumption_l100km)
                            @php $c = $r->consumption_l100km; @endphp
                            <span style="color:{{ $c > 12 ? 'var(--c-danger)' : ($c > 10 ? 'var(--c-warn)' : 'var(--c-ok)') }};font-weight:600">{{ number_format($c, 1) }}</span>
                        @else — @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="openFuelDetail({{ $r->id }})">📎</button>
                        @if($r->files->count())<span style="font-size:10px;color:var(--c-muted)">{{ $r->files->count() }}</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod bahan api</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add Fuel Modal -->
    <div class="modal-overlay" id="addFuelModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">⛽ Log Pengisian Bahan Api</div>
                <div class="modal-close" onclick="closeModal('addFuelModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('fuel.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Kenderaan *</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">Pilih kenderaan...</option>
                            @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Tarikh & Masa *</label>
                        <input name="datetime" class="form-control" type="datetime-local" required value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Stesen Minyak</label>
                        <input name="station" class="form-control" placeholder="Petronas / Shell / BHPetrol...">
                    </div>
                    <div class="form-group"><label class="form-label">Jenis Bahan Api</label>
                        <select name="fuel_type" class="form-control">
                            <option value="RON95">RON95 (RM 2.05/L)</option>
                            <option value="RON97">RON97 (RM 3.47/L)</option>
                            <option value="Diesel">Diesel (RM 3.35/L)</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jumlah Liter *</label>
                        <input name="liters" class="form-control" type="number" step="0.01" placeholder="0" required>
                    </div>
                    <div class="form-group"><label class="form-label">Odometer (km) *</label>
                        <input name="odometer_km" class="form-control" type="number" placeholder="0" required>
                    </div>
                </div>
                <div class="alert alert-info" style="margin-top:4px">
                    <span>ℹ️</span>
                    <div style="font-size:12px">Penggunaan L/100km dikira automatik berdasarkan perbezaan odometer dengan rekod sebelumnya.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addFuelModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Log</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fuel Detail / Upload Modal -->
    <div class="modal-overlay" id="fuelDetailModal">
        <div class="modal" style="max-width:480px">
            <div class="modal-header">
                <div class="modal-title" id="fdTitle">📎 Lampiran Resit</div>
                <div class="modal-close" onclick="closeModal('fuelDetailModal')">✕</div>
            </div>
            <div id="fdBody"></div>
            <div id="fdFiles"></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('fuelDetailModal')">Tutup</button></div>
        </div>
    </div>

    @foreach($records as $r)
    <div id="fuel-files-{{ $r->id }}" style="display:none">
        <x-file-upload type="fuel" :id="$r->id" :files="$r->files" />
    </div>
    @endforeach

    <script>
    const fuelData = @json($records->keyBy('id'));

    function openFuelDetail(id) {
        const f = fuelData[id];
        if (!f) return;
        document.getElementById('fdTitle').textContent = '📎 Resit — ' + f.vehicle.plat + ' (' + new Date(f.datetime).toLocaleDateString('ms-MY') + ')';
        document.getElementById('fdBody').innerHTML = `
            <div class="detail-row"><div class="detail-label">Kenderaan</div><div class="detail-val"><strong>${f.vehicle.plat}</strong> — ${f.vehicle.model}</div></div>
            <div class="detail-row"><div class="detail-label">Tarikh</div><div class="detail-val">${new Date(f.datetime).toLocaleDateString('ms-MY',{day:'numeric',month:'long',year:'numeric'})}</div></div>
            <div class="detail-row"><div class="detail-label">Stesen</div><div class="detail-val">${f.station || '—'}</div></div>
            <div class="detail-row"><div class="detail-label">Jumlah</div><div class="detail-val"><strong>RM ${parseFloat(f.total_cost).toFixed(2)}</strong> (${parseFloat(f.liters).toFixed(1)}L)</div></div>
        `;
        const filesEl = document.getElementById('fuel-files-' + id);
        document.getElementById('fdFiles').innerHTML = filesEl ? filesEl.innerHTML : '';
        document.getElementById('fuelDetailModal').classList.add('open');
    }

    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
