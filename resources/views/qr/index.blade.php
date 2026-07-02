<x-fleet-layout title="QR Kenderaan">
    <div class="page-header">
        <h1>Pengurusan QR Kenderaan</h1>
        <p>Jana, cetak dan urus QR code untuk setiap kenderaan — staff scan untuk akses pantas</p>
    </div>

    <!-- How it works -->
    <div class="card mb20">
        <div class="card-header"><span class="card-title"><span class="icon-accent"><x-icon name="qr-code" :size="17" /></span>Cara Penggunaan QR</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center">
                @foreach([
                    ['icon' => 'qr-code', 'kind' => 'accent', 'title' => 'QR Dilekat di Kenderaan', 'sub' => 'Satu QR unik per kenderaan, dicetak dan dilekat'],
                    ['icon' => 'clock', 'kind' => 'info', 'title' => 'Staff / Penjaga Scan', 'sub' => 'Guna kamera telefon — akses terus'],
                    ['icon' => 'clipboard-check', 'kind' => 'ok', 'title' => 'Form Automatik', 'sub' => 'Log keluar, minyak, atau check-in'],
                    ['icon' => 'check', 'kind' => 'ok', 'title' => 'Data Terus Tersimpan', 'sub' => 'Rekod masuk ke sistem secara automatik'],
                ] as $step)
                <div>
                    <div class="soft-{{ $step['kind'] }}" style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px"><x-icon :name="$step['icon']" :size="21" /></div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:4px">{{ $step['title'] }}</div>
                    <div style="font-size:11px;color:var(--muted)">{{ $step['sub'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- QR Grid -->
    <div style="font-size:13px;font-weight:600;margin-bottom:14px">QR Kenderaan — Klik untuk pratonton & cetak</div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:20px">
        @php
            $isTruck = fn($v) => collect(['truck', 'lori', 'pickup', 'pikap', 'van', 'd-max', 'hilux', 'triton', 'navara'])
                ->contains(fn($kw) => str_contains(strtolower($v->type . ' ' . $v->model), $kw));
        @endphp
        @foreach($vehicles as $v)
        <div class="card" style="cursor:pointer" onclick="showQR({{ $v->id }}, '{{ $v->plat }}', '{{ $v->model }}', '{{ $isTruck($v) ? 'truck' : 'car' }}', '{{ $v->qr_code_token }}')">
            <div class="card-body" style="display:flex;align-items:center;gap:14px;padding:14px 16px">
                <div class="soft-accent" style="width:50px;height:50px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><x-icon :name="$isTruck($v) ? 'truck' : 'car'" :size="24" /></div>
                <div style="flex:1">
                    <div style="font-size:16px;font-weight:700;letter-spacing:1px">{{ $v->plat }}</div>
                    <div style="font-size:12px;color:var(--muted)">{{ $v->model }} {{ $v->year }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $v->department }}</div>
                </div>
                <div style="text-align:center;color:var(--muted)">
                    <x-icon name="qr-code" :size="26" />
                    <div style="font-size:10px;margin-top:2px">QR Code</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- QR Preview Modal -->
    <div class="modal-overlay" id="qrModal">
        <div class="modal" style="max-width:420px;text-align:center">
            <div class="modal-header">
                <div class="modal-title" id="qrModalTitle">QR Kenderaan</div>
                <div class="modal-close" onclick="closeModal('qrModal')">✕</div>
            </div>
            <div id="qrModalBody" style="padding:20px 0">
                <div style="color:var(--accent-dark);margin-bottom:12px;display:flex;justify-content:center" id="qrEmoji"></div>
                <div id="qrDisplay" style="display:inline-block;padding:16px;background:#fff;border:2px solid var(--border);border-radius:12px"></div>
                <div style="margin-top:12px">
                    <div style="font-size:20px;font-weight:800;letter-spacing:2px" id="qrPlat"></div>
                    <div style="font-size:13px;color:var(--c-muted)" id="qrModel"></div>
                </div>
                <div style="margin-top:12px;font-size:11px;color:var(--c-muted)">
                    Scan QR ini menggunakan kamera telefon untuk akses pantas
                </div>
                <div style="margin-top:8px">
                    <input type="text" id="qrUrl" class="form-control" readonly style="text-align:center;font-size:11px;color:var(--c-muted)">
                </div>
            </div>
            <div class="modal-footer" style="justify-content:center;gap:10px">
                <button class="btn btn-secondary" onclick="closeModal('qrModal')">Tutup</button>
                <button class="btn btn-primary" onclick="printQR()"><x-icon name="printer" :size="15" /> Cetak QR</button>
                <button class="btn btn-secondary" id="btnSimulate"><x-icon name="qr-code" :size="15" /> Buka Scan Page</button>
            </div>
        </div>
    </div>

    <script>
    const VEHICLE_ICON_PATHS = {
        car: '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>',
        truck: '<path d="M14 18V6a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h1"/><path d="M14 9h4l4 4v4a1 1 0 0 1-1 1h-1"/><circle cx="7" cy="18" r="2"/><path d="M9 18h6"/><circle cx="17" cy="18" r="2"/>',
    };

    function showQR(id, plat, model, vehicleType, token) {
        document.getElementById('qrModalTitle').textContent = 'QR — ' + plat;
        document.getElementById('qrPlat').textContent = plat;
        document.getElementById('qrModel').textContent = model;
        document.getElementById('qrEmoji').innerHTML = '<svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">' + (VEHICLE_ICON_PATHS[vehicleType] || VEHICLE_ICON_PATHS.car) + '</svg>';

        const url = '/qr/scan/' + token;
        const fullUrl = window.location.origin + url;
        document.getElementById('qrUrl').value = fullUrl;
        document.getElementById('btnSimulate').onclick = () => window.open(url, '_blank');

        fetch('/qr/generate/' + id)
            .then(r => r.json())
            .then(data => { document.getElementById('qrDisplay').innerHTML = data.qr; })
            .catch(() => {
                document.getElementById('qrDisplay').innerHTML = '<div style="width:250px;height:250px;background:#f4f6fb;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:14px;color:var(--c-muted)">QR Code<br>' + plat + '</div>';
            });

        document.getElementById('qrModal').classList.add('open');
    }

    function printQR() {
        const content = document.getElementById('qrModalBody').innerHTML;
        const w = window.open('', '_blank');
        w.document.write('<html><head><title>QR Print</title><style>body{font-family:Segoe UI,sans-serif;text-align:center;padding:40px}svg{max-width:300px}</style></head><body>' + content + '</body></html>');
        w.document.close();
        w.print();
    }

    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
