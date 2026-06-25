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
        @can('create', App\Models\Vehicle::class)
        <button class="btn btn-primary" onclick="document.getElementById('addVehicleModal').classList.add('open')">+ Tambah Kenderaan</button>
        @endcan
    </div>

    <div class="vehicle-grid">
        @foreach($vehicles as $v)
            @php
                $rtColor = $v->roadtax_days <= 7 ? 'var(--c-danger)' : ($v->roadtax_days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                $insColor = $v->insurance_days <= 7 ? 'var(--c-danger)' : ($v->insurance_days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                $srvDays = $v->next_service_date ? (int) now()->diffInDays($v->next_service_date, false) : 999;
                $srvColor = $srvDays <= 14 ? 'var(--c-warn)' : 'var(--c-ok)';
            @endphp
            <div class="vehicle-card" onclick="openEditVehicle({{ $v->id }})" style="cursor:pointer">
                <div class="vehicle-img">{{ $v->emoji }}</div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div>
                        <div class="vehicle-plat">{{ $v->plat }}</div>
                        <div class="vehicle-model">{{ $v->model }} {{ $v->year }}</div>
                    </div>
                    @if($v->status === 'servis')
                        <span class="badge-pill badge-warn">Dalam Servis</span>
                    @elseif($v->status === 'rosak')
                        <span class="badge-pill badge-danger">Rosak</span>
                    @else
                        <span class="badge-pill badge-ok">Aktif</span>
                    @endif
                </div>
                <div style="font-size:11px;color:var(--c-muted);margin-top:4px">📁 {{ $v->department ?? '—' }} &nbsp;|&nbsp; {{ number_format($v->odometer_km) }} km</div>
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
                        <div class="val" style="color:{{ $srvColor }}">{{ $srvDays < 999 ? $srvDays . 'h' : '—' }}</div>
                        <div class="lbl">Servis</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($vehicles->isEmpty())
        <div style="text-align:center;padding:40px;color:var(--c-muted)">
            <div style="font-size:48px;margin-bottom:12px;opacity:0.3">🚗</div>
            <p>Tiada kenderaan dijumpai</p>
        </div>
    @endif

    <!-- Add Vehicle Modal -->
    <div class="modal-overlay" id="addVehicleModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">🚗 Tambah Kenderaan Baharu</div>
                <div class="modal-close" onclick="closeModal('addVehicleModal')">✕</div>
            </div>
            <form method="POST" action="{{ route('vehicles.store') }}">
                @csrf
                @include('vehicles._form')
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addVehicleModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kenderaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div class="modal-overlay" id="editVehicleModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">✏️ Kemaskini Kenderaan</div>
                <div class="modal-close" onclick="closeModal('editVehicleModal')">✕</div>
            </div>
            <form method="POST" id="editVehicleForm">
                @csrf
                @method('PUT')
                @include('vehicles._form', ['prefix' => 'edit_'])
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteVehicle()" style="margin-right:auto">Padam</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editVehicleModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kemaskini</button>
                </div>
            </form>
            <form method="POST" id="deleteVehicleForm" style="display:none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    @if($errors->any())
    <script>document.getElementById('addVehicleModal').classList.add('open');</script>
    @endif

    <script>
    const vehiclesData = @json($vehicles->keyBy('id'));

    function openEditVehicle(id) {
        const v = vehiclesData[id];
        if (!v) return;
        const prefix = 'edit_';
        document.getElementById(prefix + 'plat').value = v.plat || '';
        document.getElementById(prefix + 'model').value = v.model || '';
        document.getElementById(prefix + 'type').value = v.type || '';
        document.getElementById(prefix + 'year').value = v.year || '';
        document.getElementById(prefix + 'color').value = v.color || '';
        document.getElementById(prefix + 'department').value = v.department || '';
        document.getElementById(prefix + 'engine_no').value = v.engine_no || '';
        document.getElementById(prefix + 'chassis_no').value = v.chassis_no || '';
        document.getElementById(prefix + 'roadtax_expiry').value = v.roadtax_expiry ? v.roadtax_expiry.split('T')[0] : '';
        document.getElementById(prefix + 'insurance_expiry').value = v.insurance_expiry ? v.insurance_expiry.split('T')[0] : '';
        document.getElementById(prefix + 'odometer_km').value = v.odometer_km || '';
        document.getElementById(prefix + 'status').value = v.status || 'aktif';

        document.getElementById('editVehicleForm').action = '/vehicles/' + id;
        document.getElementById('deleteVehicleForm').action = '/vehicles/' + id;
        document.getElementById('editVehicleModal').classList.add('open');
    }

    function deleteVehicle() {
        if (confirm('Padam kenderaan ini? Tindakan ini tidak boleh dibatalkan.')) {
            document.getElementById('deleteVehicleForm').submit();
        }
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
    });
    </script>
</x-fleet-layout>
