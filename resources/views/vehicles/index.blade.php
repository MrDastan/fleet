<x-fleet-layout title="Senarai Kenderaan">
    <div class="page-header">
        <h2>Senarai Kenderaan</h2>
        <p>Semua kenderaan syarikat beserta status terkini</p>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('vehicles.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;flex:1">
            <input type="text" name="search" class="form-control search-input" placeholder="🔍  Cari nombor plat, model..." value="{{ request('search') }}">
            <select name="status" class="form-control" style="width:160px" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="servis" {{ request('status') === 'servis' ? 'selected' : '' }}>Dalam Servis</option>
                <option value="rosak" {{ request('status') === 'rosak' ? 'selected' : '' }}>Rosak</option>
            </select>
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addVehicleModal').classList.add('open')">+ Tambah Kenderaan</button>
    </div>

    <div class="vehicle-grid">
        @foreach($vehicles as $v)
            @php
                $rtColor = $v->roadtax_days <= 7 ? 'var(--c-danger)' : ($v->roadtax_days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                $insColor = $v->insurance_days <= 7 ? 'var(--c-danger)' : ($v->insurance_days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
            @endphp
            <div class="vehicle-card">
                <div class="vehicle-img">{{ $v->emoji }}</div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div>
                        <div class="vehicle-plat">{{ $v->plat }}</div>
                        <div class="vehicle-model">{{ $v->model }} {{ $v->year }}</div>
                    </div>
                    @if($v->status === 'servis')
                        <span class="badge-pill badge-warn">Dalam Servis</span>
                    @elseif($v->status === 'aktif')
                        <span class="badge-pill badge-ok">Aktif</span>
                    @else
                        <span class="badge-pill badge-danger">{{ ucfirst($v->status) }}</span>
                    @endif
                </div>
                <div style="font-size:11px;color:var(--c-muted);margin-top:4px">📁 {{ $v->department }} &nbsp;|&nbsp; {{ number_format($v->odometer_km) }} km</div>
                <div class="vehicle-stats">
                    <div class="vehicle-stat">
                        <div class="val" style="color:{{ $rtColor }}">{{ $v->roadtax_days }}h</div>
                        <div class="lbl">Road Tax</div>
                    </div>
                    <div class="vehicle-stat">
                        <div class="val" style="color:{{ $insColor }}">{{ $v->insurance_days }}h</div>
                        <div class="lbl">Insuran</div>
                    </div>
                    <div class="vehicle-stat">
                        <div class="val">{{ $v->odometer_km > 0 ? number_format($v->odometer_km / 1000, 0) . 'K' : '—' }}</div>
                        <div class="lbl">KM</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Vehicle Modal -->
    <div class="modal-overlay" id="addVehicleModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">🚗 Tambah Kenderaan Baharu</div>
                <div class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</div>
            </div>
            <form method="POST" action="{{ route('vehicles.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">No. Plat *</label><input name="plat" class="form-control" placeholder="Contoh: WXY 1234" required></div>
                    <div class="form-group"><label class="form-label">Model Kenderaan *</label><input name="model" class="form-control" placeholder="Contoh: Toyota Hilux" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jenis</label><input name="type" class="form-control" placeholder="Sedan / SUV / Pikap"></div>
                    <div class="form-group"><label class="form-label">Tahun</label><input name="year" class="form-control" type="number" placeholder="2024"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Warna</label><input name="color" class="form-control" placeholder="Putih"></div>
                    <div class="form-group"><label class="form-label">Jabatan</label>
                        <select name="department" class="form-control">
                            <option value="">Pilih jabatan...</option>
                            <option>Operasi</option><option>Pemasaran</option><option>IT</option><option>Kewangan</option><option>HR</option><option>Pengurusan</option><option>Pentadbiran</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">No. Enjin</label><input name="engine_no" class="form-control"></div>
                    <div class="form-group"><label class="form-label">No. Casis</label><input name="chassis_no" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Tarikh Luput Road Tax</label><input name="roadtax_expiry" class="form-control" type="date"></div>
                    <div class="form-group"><label class="form-label">Tarikh Luput Insuran</label><input name="insurance_expiry" class="form-control" type="date"></div>
                </div>
                <div class="form-group"><label class="form-label">Odometer Semasa (km)</label><input name="odometer_km" class="form-control" type="number" placeholder="0"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kenderaan</button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any())
    <script>document.getElementById('addVehicleModal').classList.add('open');</script>
    @endif
</x-fleet-layout>
