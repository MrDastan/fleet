<x-fleet-layout title="Rekod Bahan Api">
    <div class="page-header">
        <h2>Rekod Bahan Api</h2>
        <p>Log pengisian bahan api dan analisis penggunaan</p>
    </div>

    <div class="search-bar">
        <input type="text" class="form-control search-input" placeholder="🔍  Cari rekod bahan api...">
        <button class="btn btn-primary" onclick="document.getElementById('addFuelModal').classList.add('open')">+ Log Pengisian</button>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>Tarikh</th><th>No. Plat</th><th>Pemandu</th><th>Stesen</th><th>Liter</th><th>Jumlah</th><th>Odometer</th></tr></thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td>{{ $r->datetime->format('d M, H:i') }}</td>
                    <td><strong>{{ $r->vehicle->plat }}</strong></td>
                    <td>{{ $r->driver?->name ?? '—' }}</td>
                    <td>{{ $r->station ?? '—' }}</td>
                    <td>{{ $r->liters }}L</td>
                    <td><strong>RM {{ number_format($r->total_cost, 2) }}</strong></td>
                    <td>{{ number_format($r->odometer_km) }} km</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod bahan api</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="addFuelModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">⛽ Log Pengisian Bahan Api</div>
                <div class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</div>
            </div>
            <form method="POST" action="{{ route('fuel.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Kenderaan *</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">Pilih...</option>
                            @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Tarikh & Masa *</label><input name="datetime" class="form-control" type="datetime-local" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Stesen</label><input name="station" class="form-control" placeholder="Petronas / Shell..."></div>
                    <div class="form-group"><label class="form-label">Jenis</label>
                        <select name="fuel_type" class="form-control"><option>RON95</option><option>RON97</option><option>Diesel</option></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Liter *</label><input name="liters" class="form-control" type="number" step="0.01" required></div>
                    <div class="form-group"><label class="form-label">Odometer (km) *</label><input name="odometer_km" class="form-control" type="number" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Log</button>
                </div>
            </form>
        </div>
    </div>
</x-fleet-layout>
