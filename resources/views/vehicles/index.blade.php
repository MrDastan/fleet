@php
    $dc = fn($days) => $days <= 7 ? 'var(--danger-text)' : ($days <= 30 ? 'var(--warn-text)' : 'var(--ok)');
    $isTruck = function ($v) {
        $s = strtolower($v->type . ' ' . $v->model);
        foreach (['truck', 'lori', 'pickup', 'pikap', 'van', 'd-max', 'hilux', 'triton', 'navara'] as $kw) {
            if (str_contains($s, $kw)) return true;
        }
        return false;
    };
    $chips = [
        ['status' => null, 'label' => 'Semua'],
        ['status' => 'aktif', 'label' => 'Aktif'],
        ['status' => 'servis', 'label' => 'Dalam Servis'],
        ['status' => 'rosak', 'label' => 'Rosak'],
    ];
@endphp
<x-fleet-layout title="Senarai Kenderaan">
    <div class="page-header">
        <h1>Senarai Kenderaan</h1>
        <p>{{ \App\Models\Vehicle::count() }} kenderaan syarikat · {{ $vehicles->count() }} dipaparkan</p>
    </div>

    <form method="GET" action="{{ route('vehicles.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:14px">
        <input type="text" name="search" class="form-control" style="max-width:260px" placeholder="Cari nombor plat, model..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
    </form>

    <div class="chip-row">
        @foreach($chips as $c)
            <a href="{{ route('vehicles.index', array_filter(['status' => $c['status'], 'search' => request('search')])) }}"
               class="chip {{ request('status') === $c['status'] ? 'active' : '' }}">{{ $c['label'] }}</a>
        @endforeach
        @can('create', App\Models\Vehicle::class)
        <button class="btn btn-primary" style="margin-left:auto" onclick="document.getElementById('addVehicleModal').classList.add('open')"><x-icon name="plus" :size="16" /> Tambah Kenderaan</button>
        @endcan
    </div>

    <div class="vehicle-grid">
        @foreach($vehicles as $v)
            @php
                $truck = $isTruck($v);
                $tileClass = $truck ? 'background:#EDEAE3;color:#5A544B' : 'background:#FCEBE0;color:#C0480A';
                $srvDays = $v->next_service_date ? (int) now()->diffInDays($v->next_service_date, false) : null;
            @endphp
            <div class="vehicle-card" onclick="openEditVehicle({{ $v->id }})">
                <div class="vehicle-tile" style="{{ $tileClass }}">
                    <x-icon :name="$truck ? 'truck' : 'car'" :size="42" :stroke="1.5" />
                    <span class="status-badge">
                        @if($v->status === 'servis')
                            <span class="badge-pill badge-warn">Servis</span>
                        @elseif($v->status === 'rosak')
                            <span class="badge-pill badge-danger">Rosak</span>
                        @else
                            <span class="badge-pill badge-ok">Aktif</span>
                        @endif
                    </span>
                </div>
                <div class="vehicle-card-body">
                    <div class="vehicle-card-top">
                        <span class="vehicle-plat">{{ $v->plat }}</span>
                        <span class="vehicle-km">{{ number_format($v->odometer_km) }} km</span>
                    </div>
                    <div class="vehicle-model">{{ $v->model }} {{ $v->year }} · {{ $v->department ?? '—' }}</div>
                    <div class="vehicle-stats">
                        <div class="vehicle-stat">
                            <div class="val" style="color:{{ $dc($v->roadtax_days) }}">{{ $v->roadtax_days }}h</div>
                            <div class="lbl">Road Tax</div>
                        </div>
                        <div class="vehicle-stat">
                            <div class="val" style="color:{{ $dc($v->insurance_days) }}">{{ $v->insurance_days }}h</div>
                            <div class="lbl">Insuran</div>
                        </div>
                        <div class="vehicle-stat">
                            <div class="val" style="color:{{ $srvDays !== null ? $dc($srvDays) : 'var(--muted)' }}">{{ $srvDays !== null ? $srvDays . 'h' : '—' }}</div>
                            <div class="lbl">Servis</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($vehicles->isEmpty())
        <div style="text-align:center;padding:40px;color:var(--muted)">
            <div style="margin-bottom:12px;opacity:0.3;display:flex;justify-content:center"><x-icon name="car" :size="48" :stroke="1.3" /></div>
            <p>Tiada kenderaan dijumpai</p>
        </div>
    @endif

    <!-- Add Vehicle Modal -->
    <div class="modal-overlay" id="addVehicleModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title"><x-icon name="car" :size="18" /> Tambah Kenderaan Baharu</div>
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
                <div class="modal-title"><x-icon name="wrench" :size="18" /> Kemaskini Kenderaan</div>
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
