<x-fleet-layout title="Log Pergerakan">
    <div class="page-header">
        <h2>Log Pergerakan & Check-in/out</h2>
        <p>Log keluar masuk kenderaan di stor / parkir syarikat</p>
    </div>

    @if($outCount > 0)
    <div class="alert alert-info">
        <span>🚗</span>
        <div><strong>{{ $outCount }} kenderaan</strong> sedang di luar. Pastikan semua kenderaan di-check-in sebelum tamat hari.</div>
    </div>
    @endif

    <div class="search-bar">
        <form method="GET" action="{{ route('movements.index') }}" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
            <input type="text" name="search" class="form-control search-input" placeholder="🔍  Cari nama / plat / tujuan..." value="{{ request('search') }}">
            <input type="date" name="date" class="form-control" style="width:160px" value="{{ request('date', date('Y-m-d')) }}" onchange="this.form.submit()">
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('checkoutModal').classList.add('open')">+ Log Keluar</button>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead>
                <tr>
                    <th>No. Plat</th>
                    <th>Pemandu</th>
                    <th>Jabatan</th>
                    <th>Tujuan</th>
                    <th>Masa Keluar</th>
                    <th>Masa Masuk</th>
                    <th>Km Keluar</th>
                    <th>Km Masuk</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $l)
                <tr>
                    <td><strong>{{ $l->vehicle->plat }}</strong><div style="font-size:11px;color:var(--c-muted)">{{ $l->vehicle->model }}</div></td>
                    <td>{{ $l->driver?->name ?? '—' }}</td>
                    <td>{{ $l->department ?? '—' }}</td>
                    <td>{{ $l->purpose }}<div style="font-size:11px;color:var(--c-muted)">{{ $l->destination }}</div></td>
                    <td>{{ $l->checkout_time?->format('H:i') ?? '—' }}</td>
                    <td>{{ $l->checkin_time?->format('H:i') ?? '—' }}</td>
                    <td>{{ $l->km_out ? number_format($l->km_out) : '—' }}</td>
                    <td>{{ $l->km_in ? number_format($l->km_in) : '—' }}</td>
                    <td>
                        @if($l->status === 'di_luar')
                            <span class="badge-pill badge-ok">Di Luar</span>
                        @else
                            <span class="badge-pill badge-neutral">Kembali</span>
                        @endif
                    </td>
                    <td>
                        @if($l->status === 'di_luar')
                            <button class="btn btn-sm btn-primary" onclick="openCheckin({{ $l->id }}, '{{ $l->vehicle->plat }}', {{ $l->km_out ?? 0 }})">Check-in</button>
                        @else
                            @if($l->km_out && $l->km_in)
                                <span style="font-size:11px;color:var(--c-muted)">{{ $l->km_in - $l->km_out }} km</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod pergerakan untuk tarikh ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Checkout Modal -->
    <div class="modal-overlay" id="checkoutModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">📋 Log Keluar Kenderaan</div>
                <div class="modal-close" onclick="closeModal('checkoutModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('movements.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Kenderaan *</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">Pilih kenderaan...</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Jabatan</label>
                        <input name="department" class="form-control" value="{{ auth()->user()->department }}">
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Tujuan Perjalanan *</label>
                    <input name="purpose" class="form-control" placeholder="Lawatan klien, urusan rasmi..." required>
                </div>
                <div class="form-group"><label class="form-label">Destinasi</label>
                    <input name="destination" class="form-control" placeholder="Nama tempat / kawasan">
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Odometer Keluar (km)</label>
                        <input name="km_out" class="form-control" type="number">
                    </div>
                    <div class="form-group"><label class="form-label">Masa Keluar *</label>
                        <input name="checkout_time" class="form-control" type="datetime-local" required value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Catatan Penjaga</label>
                    <textarea name="guard_notes" class="form-control" rows="2" placeholder="Kondisi kenderaan, nota..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('checkoutModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Log Keluar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Checkin Modal -->
    <div class="modal-overlay" id="checkinModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="checkinTitle">📋 Log Masuk Kenderaan</div>
                <div class="modal-close" onclick="closeModal('checkinModal')">✕</div>
            </div>
            <form method="POST" id="checkinForm">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Odometer Masuk (km)</label>
                        <input name="km_in" id="checkinKm" class="form-control" type="number">
                    </div>
                    <div class="form-group"><label class="form-label">Masa Masuk *</label>
                        <input name="checkin_time" class="form-control" type="datetime-local" required value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Catatan</label>
                    <textarea name="guard_notes" class="form-control" rows="2" placeholder="Kondisi pulangan, kerosakan..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('checkinModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Log Masuk</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openCheckin(id, plat, kmOut) {
        document.getElementById('checkinTitle').textContent = '📋 Log Masuk — ' + plat;
        document.getElementById('checkinForm').action = '/movements/' + id + '/checkin';
        document.getElementById('checkinKm').value = '';
        document.getElementById('checkinKm').placeholder = kmOut ? 'Min: ' + kmOut.toLocaleString() : '0';
        document.getElementById('checkinModal').classList.add('open');
    }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
