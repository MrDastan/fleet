<x-fleet-layout title="Road Tax & Insuran">
    <div class="page-header">
        <h2>Road Tax & Insuran</h2>
        <p>Pantau tarikh luput road tax dan polisi insuran semua kenderaan</p>
    </div>

    <div class="grid-2" style="margin-bottom:20px">
        <div class="stat-card" style="border-left:4px solid var(--c-danger)">
            <div style="font-size:12px;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Akan Luput ≤ 30 Hari</div>
            <div style="display:flex;gap:24px;margin-top:10px">
                <div><div style="font-size:28px;font-weight:800;color:var(--c-danger)">{{ $rtExpiring }}</div><div style="font-size:12px;color:var(--c-muted)">Road Tax</div></div>
                <div><div style="font-size:28px;font-weight:800;color:var(--c-danger)">{{ $insExpiring }}</div><div style="font-size:12px;color:var(--c-muted)">Insuran</div></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid var(--c-ok)">
            <div style="font-size:12px;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Status Baik (> 30 Hari)</div>
            <div style="display:flex;gap:24px;margin-top:10px">
                <div><div style="font-size:28px;font-weight:800;color:var(--c-ok)">{{ $rtOk }}</div><div style="font-size:12px;color:var(--c-muted)">Road Tax</div></div>
                <div><div style="font-size:28px;font-weight:800;color:var(--c-ok)">{{ $insOk }}</div><div style="font-size:12px;color:var(--c-muted)">Insuran</div></div>
            </div>
        </div>
    </div>

    <div class="tabs">
        <a href="{{ route('roadtax.index', ['tab' => 'roadtax']) }}" class="tab {{ $tab === 'roadtax' ? 'active' : '' }}">Road Tax</a>
        <a href="{{ route('roadtax.index', ['tab' => 'insuran']) }}" class="tab {{ $tab === 'insuran' ? 'active' : '' }}">Insuran</a>
        <a href="{{ route('roadtax.index', ['tab' => 'puspakom']) }}" class="tab {{ $tab === 'puspakom' ? 'active' : '' }}">Puspakom</a>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">
                @if($tab === 'roadtax') 📄 Senarai Road Tax
                @elseif($tab === 'insuran') 🛡️ Senarai Insuran
                @else 📋 Senarai Puspakom @endif
            </span>
            <button class="btn btn-sm btn-primary" onclick="document.getElementById('addRoadtaxModal').classList.add('open')">+ Kemaskini</button>
        </div>
        <table class="fleet-table">
            <thead>
                <tr>
                    <th>No. Plat</th>
                    <th>Model</th>
                    <th>Tarikh Luput</th>
                    <th>Baki Hari</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles->sortBy(fn($v) => $tab === 'insuran' ? $v->insurance_days : $v->roadtax_days) as $v)
                    @php
                        if ($tab === 'insuran') {
                            $expiry = $v->insurance_expiry;
                            $days = $v->insurance_days;
                        } elseif ($tab === 'puspakom') {
                            $expiry = $v->puspakom_expiry;
                            $days = $expiry ? (int) now()->diffInDays($expiry, false) : 999;
                        } else {
                            $expiry = $v->roadtax_expiry;
                            $days = $v->roadtax_days;
                        }
                        $color = $days <= 7 ? 'var(--c-danger)' : ($days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                    @endphp
                    <tr>
                        <td><strong>{{ $v->plat }}</strong></td>
                        <td>{{ $v->model }}</td>
                        <td>{{ $expiry?->format('d M Y') ?? '—' }}</td>
                        <td><span style="color:{{ $color }};font-weight:700">{{ $expiry ? $days . ' hari' : '—' }}</span></td>
                        <td>
                            @if($days <= 7)<span class="badge-pill badge-danger">⚠ Urgent</span>
                            @elseif($days <= 30)<span class="badge-pill badge-warn">Segera</span>
                            @else<span class="badge-pill badge-ok">Aktif</span>@endif
                        </td>
                        <td>
                            @if($days <= 30)
                                <button class="btn btn-sm btn-primary" onclick="openRenew({{ $v->id }}, '{{ $v->plat }}', '{{ $tab }}')">Perbaharui</button>
                            @else
                                <button class="btn btn-sm btn-secondary" onclick="openRenew({{ $v->id }}, '{{ $v->plat }}', '{{ $tab }}')">Detail</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($records->where('doc_type', $tab)->count())
    <div class="card" style="margin-top:20px">
        <div class="card-header"><span class="card-title">📂 Sejarah Pembaharuan</span></div>
        <table class="fleet-table">
            <thead><tr><th>No. Plat</th><th>Jenis</th><th>Tarikh Mula</th><th>Tarikh Luput</th><th>Jumlah</th><th>No. Polisi</th></tr></thead>
            <tbody>
                @foreach($records->where('doc_type', $tab) as $r)
                <tr>
                    <td><strong>{{ $r->vehicle->plat }}</strong></td>
                    <td>{{ ucfirst($r->doc_type) }}</td>
                    <td>{{ $r->start_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $r->expiry_date->format('d M Y') }}</td>
                    <td>{{ $r->amount ? 'RM ' . number_format($r->amount, 2) : '—' }}</td>
                    <td>{{ $r->policy_no ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Add/Renew Modal -->
    <div class="modal-overlay" id="addRoadtaxModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="rtModalTitle">📄 Kemaskini Road Tax / Insuran</div>
                <div class="modal-close" onclick="closeModal('addRoadtaxModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('roadtax.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Kenderaan *</label>
                    <select name="vehicle_id" id="rtVehicle" class="form-control" required>
                        <option value="">Pilih kenderaan...</option>
                        @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->plat }} — {{ $v->model }}</option>@endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jenis Dokumen</label>
                        <select name="doc_type" id="rtDocType" class="form-control">
                            <option value="roadtax">Road Tax</option>
                            <option value="insuran">Insuran</option>
                            <option value="puspakom">Puspakom</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Tarikh Mula</label><input name="start_date" class="form-control" type="date"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Tarikh Luput *</label><input name="expiry_date" class="form-control" type="date" required></div>
                    <div class="form-group"><label class="form-label">Jumlah Dibayar (RM)</label><input name="amount" class="form-control" type="number" step="0.01"></div>
                </div>
                <div class="form-group"><label class="form-label">No. Polisi / Rujukan</label><input name="policy_no" class="form-control" placeholder="No. polisi insuran atau rujukan"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addRoadtaxModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openRenew(vehicleId, plat, docType) {
        document.getElementById('rtVehicle').value = vehicleId;
        document.getElementById('rtDocType').value = docType;
        document.getElementById('rtModalTitle').textContent = '📄 Perbaharui ' + (docType === 'insuran' ? 'Insuran' : docType === 'puspakom' ? 'Puspakom' : 'Road Tax') + ' — ' + plat;
        document.getElementById('addRoadtaxModal').classList.add('open');
    }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
